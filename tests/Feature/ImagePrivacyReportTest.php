<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Models\ImagePrivacyReport;
use App\Models\User;
use App\Services\BusinessPhotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImagePrivacyReportTest extends TestCase
{
    use RefreshDatabase;

    private function provider(): User
    {
        Storage::fake('public');
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['business_name' => 'Reported provider']);
        $photos = app(BusinessPhotoService::class);
        $photos->updateProfilePhoto($profile, UploadedFile::fake()->image('profile.jpg'));
        $photos->updateBrandPhoto($profile, UploadedFile::fake()->image('cover.png'));
        $photos->addWorkSamples($profile, [UploadedFile::fake()->image('gallery.jpg'), UploadedFile::fake()->image('other.jpg')]);

        return $provider->load('businessProfile');
    }

    private function target(User $provider, string $type): array
    {
        $path = match ($type) {
            'profile' => $provider->businessProfile->profile_photo, 'brand' => $provider->businessProfile->brand_photo, default => $provider->businessProfile->work_samples[0]
        };

        return ['provider' => $provider, 'imageType' => $type, 'imageRef' => hash('sha256', $path)];
    }

    private function data(array $overrides = []): array
    {
        return [...['reason' => 'Personal information shown', 'description' => 'PRIVATE REPORT DETAILS about visible personal information.', 'contact_email' => 'visitor@example.com', 'submission_token' => (string) Str::uuid()], ...$overrides];
    }

    public function test_visitors_report_every_image_type_privately_without_auto_deletion(): void
    {
        $provider = $this->provider();
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Report profile image')->assertSee('Report cover image')->assertSee('Report gallery image');
        foreach (['profile', 'brand', 'gallery'] as $type) {
            $target = $this->target($provider, $type);
            $this->get(route('providers.images.report', $target))->assertOk()->assertSee('does not automatically remove');
            $this->post(route('providers.images.report.store', $target), $this->data())->assertRedirect(route('providers.show', $provider))->assertSessionHasNoErrors();
        }
        $this->assertSame(3, ImagePrivacyReport::count());
        $this->assertCount(4, Storage::disk('public')->allFiles());
        $report = ImagePrivacyReport::firstOrFail();
        $this->assertSame(ReportStatus::Open, $report->status);
        $this->assertNotEmpty($report->image_metadata['filename']);
        $this->assertGreaterThan(0, $report->image_metadata['size_bytes']);
        $this->get(route('providers.show', $provider))->assertOk()->assertDontSee('PRIVATE REPORT DETAILS')->assertDontSee('visitor@example.com');
        $this->get('/image-reports')->assertNotFound();
        $this->get(route('admin.image-reports.index'))->assertRedirect(route('admin.login'));
    }

    public function test_duplicate_and_forged_reports_cannot_target_arbitrary_paths_or_other_images(): void
    {
        $provider = $this->provider();
        $seeker = User::factory()->customer()->create();
        $target = $this->target($provider, 'profile');
        $data = $this->data(['image_path' => 'unrelated.txt', 'image_ref' => 'forged', 'provider_id' => 999]);
        $this->actingAs($seeker)->post(route('providers.images.report.store', $target), $data)->assertSessionHasNoErrors();
        $this->post(route('providers.images.report.store', $target), $data)->assertSessionHasNoErrors();
        $this->post(route('providers.images.report.store', $target), $this->data())->assertSessionHasNoErrors();
        $this->assertSame(1, ImagePrivacyReport::count());
        $this->assertSame($provider->businessProfile->profile_photo, ImagePrivacyReport::firstOrFail()->image_path);
        $this->get(route('providers.images.report', [...$target, 'imageRef' => str_repeat('0', 64)]))->assertNotFound();
        $this->post(route('providers.images.report.store', [...$target, 'imageRef' => str_repeat('0', 64)]), $data)->assertNotFound();
        $this->post(route('providers.images.report.store', $target), $this->data(['reason' => 'Bad', 'description' => 'short', 'contact_email' => 'bad']))->assertSessionHasErrors(['reason', 'description', 'contact_email']);
    }

    public function test_only_admin_can_review_and_remove_exact_reported_images_with_recorded_reason(): void
    {
        $provider = $this->provider();
        $paths = [$provider->businessProfile->profile_photo, $provider->businessProfile->brand_photo, ...$provider->businessProfile->work_samples];
        foreach (['profile', 'brand', 'gallery'] as $type) {
            $this->post(route('providers.images.report.store', $this->target($provider, $type)), $this->data())->assertSessionHasNoErrors();
        }
        $seeker = User::factory()->customer()->create();
        $first = ImagePrivacyReport::firstOrFail();
        $this->actingAs($seeker)->get(route('admin.image-reports.index'))->assertForbidden();
        $this->patch(route('admin.image-reports.remove', $first), ['removal_reason' => 'Unauthorized attempt'])->assertForbidden();
        $this->actingAs($provider)->patch(route('admin.image-reports.update', $first), ['status' => 'resolved'])->assertForbidden();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.image-reports.index'))->assertOk()->assertSee('PRIVATE REPORT DETAILS');
        $this->patch(route('admin.image-reports.update', $first), ['status' => 'in_review', 'internal_notes' => 'PRIVATE ADMIN IMAGE NOTE'])->assertSessionHasNoErrors();
        $this->patch(route('admin.image-reports.remove', $first), [])->assertSessionHasErrors('removal_reason');
        foreach (ImagePrivacyReport::all() as $report) {
            $this->patch(route('admin.image-reports.remove', $report), ['removal_reason' => 'Verified privacy concern; remove original image.'])->assertSessionHasNoErrors();
            $this->assertSame(ReportStatus::Resolved, $report->refresh()->status);
            $this->assertSame($admin->id, $report->removal_requested_by);
            $this->assertNotNull($report->removal_requested_at);
            $this->assertSame('Reported image removed', $report->removal_outcome);
        }
        $profile = $provider->businessProfile->refresh();
        $this->assertNull($profile->profile_photo);
        $this->assertNull($profile->brand_photo);
        $this->assertSame([$paths[3]], $profile->work_samples);
        foreach (array_slice($paths, 0, 3) as $path) {
            Storage::disk('public')->assertMissing($path);
        }
        Storage::disk('public')->assertExists($paths[3]);
        $this->get(route('providers.show', $provider))->assertOk()->assertDontSee('PRIVATE ADMIN IMAGE NOTE')->assertDontSee('PRIVATE REPORT DETAILS');
    }

    public function test_replacement_retains_original_metadata_and_admin_never_removes_replacement_or_unrelated_file(): void
    {
        $provider = $this->provider();
        $this->post(route('providers.images.report.store', $this->target($provider, 'profile')), $this->data())->assertSessionHasNoErrors();
        $report = ImagePrivacyReport::firstOrFail();
        $original = $report->image_path;
        app(BusinessPhotoService::class)->updateProfilePhoto($provider->businessProfile, UploadedFile::fake()->image('replacement.png'));
        $replacement = $provider->businessProfile->profile_photo;
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch(route('admin.image-reports.remove', $report), ['removal_reason' => 'Reviewed original image privacy concern.'])->assertSessionHasNoErrors();
        $this->assertSame($original, $report->refresh()->image_path);
        $this->assertSame(basename($original), $report->image_metadata['filename']);
        Storage::disk('public')->assertExists($replacement);
        $this->assertSame($replacement, $provider->businessProfile->fresh()->profile_photo);
        Storage::disk('public')->put('unrelated.txt', 'preserve');
        $report->update(['image_path' => 'unrelated.txt']);
        $this->patch(route('admin.image-reports.remove', $report), ['removal_reason' => 'Invalid path must never be removed.'])->assertSessionHasErrors('removal_reason');
        Storage::disk('public')->assertExists('unrelated.txt');
        $this->assertSame(ReportStatus::InReview, $report->refresh()->status);
    }

    public function test_visitor_reporting_is_rate_limited(): void
    {
        $provider = $this->provider();
        $target = $this->target($provider, 'profile');
        foreach (range(1, 5) as $number) {
            $this->post(route('providers.images.report.store', $target), $this->data(['description' => 'Privacy concern details number '.$number]))->assertSessionHasNoErrors();
        }
        $this->post(route('providers.images.report.store', $target), $this->data())->assertStatus(429);
    }

    public function test_failed_removal_audit_never_deletes_the_image(): void
    {
        $provider = $this->provider();
        $this->post(route('providers.images.report.store', $this->target($provider, 'profile')), $this->data())->assertSessionHasNoErrors();
        $report = ImagePrivacyReport::firstOrFail();
        $path = $report->image_path;
        $admin = User::factory()->admin()->create();
        ImagePrivacyReport::updating(fn () => false);
        $this->actingAs($admin)->patch(route('admin.image-reports.remove', $report), ['removal_reason' => 'Audit must be recorded before any deletion.'])->assertSessionHasErrors('removal_reason');
        Storage::disk('public')->assertExists($path);
        $this->assertSame($path, $provider->businessProfile->fresh()->profile_photo);
        $this->assertSame(ReportStatus::Open, $report->refresh()->status);
    }
}
