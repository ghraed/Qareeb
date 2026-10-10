<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\User;
use App\Services\BusinessPhotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProviderImageSafetyTest extends TestCase
{
    use RefreshDatabase;

    private function provider(): User
    {
        Storage::fake('public');
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create();

        return $provider;
    }

    public function test_supported_formats_upload_preview_replace_and_render_for_all_image_types(): void
    {
        $provider = $this->provider();
        foreach (['jpg', 'png', 'webp'] as $extension) {
            foreach (['profile-photo', 'brand-photo'] as $type) {
                $this->actingAs($provider)->post(route('business.profile.'.$type.'.store'), ['photo' => UploadedFile::fake()->image('image.'.$extension)])
                    ->assertSessionHasNoErrors();
            }
            $this->post(route('business.profile.work-samples.store'), ['photos' => [UploadedFile::fake()->image('work.'.$extension)]])->assertSessionHasNoErrors();
        }
        $profile = $provider->businessProfile->refresh();
        $this->assertCount(3, $profile->work_samples);
        $this->get(route('business.profile.edit'))->assertOk()->assertSee($profile->profilePhotoUrl(), false)->assertSee($profile->brandPhotoUrl(), false);
        $this->get(route('providers.show', $provider))->assertOk()->assertSee($profile->workSampleUrls()[0], false);
        $this->post(route('business.profile.profile-photo.store'), ['photo' => UploadedFile::fake()->image('oversize.jpg')->size(10241)])->assertSessionHasErrors('photo');
        $this->post(route('business.profile.work-samples.store'), ['photos' => [UploadedFile::fake()->image('oversize.png')->size(5121)]])->assertSessionHasErrors('photos.0');
        $this->post(route('business.profile.profile-photo.store'), ['photo' => UploadedFile::fake()->createWithContent('image.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>')])->assertSessionHasErrors('photo');
        $this->assertSame($profile->profile_photo, $profile->fresh()->profile_photo);
    }

    public function test_stale_gallery_requests_cannot_exceed_capacity_or_overwrite_new_samples(): void
    {
        $provider = $this->provider();
        $service = app(BusinessPhotoService::class);
        $first = $provider->businessProfile;
        $stale = BusinessProfile::findOrFail($first->id);
        $service->addWorkSamples($first, array_map(fn ($i) => UploadedFile::fake()->image($i.'.jpg'), range(1, 5)));
        $service->addWorkSamples($stale, [UploadedFile::fake()->image('sixth.jpg')]);
        $paths = $first->fresh()->work_samples;
        $this->assertCount(6, $paths);
        try {
            $service->addWorkSamples($first, [UploadedFile::fake()->image('overflow.jpg')]);
            $this->fail('Expected gallery capacity validation');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('photos', $exception->errors());
        }
        $this->assertSame($paths, $first->fresh()->work_samples);
        $this->assertCount(6, Storage::disk('public')->allFiles());
        $service->deleteWorkSample($first, 0, hash('sha256', $paths[0]));
        try {
            $service->deleteWorkSample($first, 0, hash('sha256', $paths[0]));
            $this->fail('Expected stale delete rejection');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('photos', $exception->errors());
        }
        Storage::disk('public')->assertExists($paths[1]);
    }

    public function test_failed_replacement_and_partial_gallery_save_preserve_previous_files_and_cleanup_new_uploads(): void
    {
        $provider = $this->provider();
        $this->actingAs($provider)->post(route('business.profile.profile-photo.store'), ['photo' => UploadedFile::fake()->image('before.jpg')]);
        $old = $provider->businessProfile->refresh()->profile_photo;
        BusinessProfile::saving(fn () => false);
        $this->post(route('business.profile.profile-photo.store'), ['photo' => UploadedFile::fake()->image('after.png')])->assertSessionHasErrors('photo');
        $this->post(route('business.profile.work-samples.store'), ['photos' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')]])->assertSessionHasErrors('photos');
        $this->assertSame($old, $provider->businessProfile->refresh()->profile_photo);
        $this->assertSame([], $provider->businessProfile->work_samples);
        $this->assertSame([$old], Storage::disk('public')->allFiles());
    }

    public function test_cleanup_never_deletes_another_owners_or_arbitrary_or_still_referenced_files(): void
    {
        $provider = $this->provider();
        $other = User::factory()->serviceProvider()->create();
        $otherPath = "businesses/{$other->id}/profile/other.jpg";
        foreach (['unrelated.txt', $otherPath, "businesses/{$provider->id}/profile/shared.jpg"] as $path) {
            Storage::disk('public')->put($path, 'keep');
            $provider->businessProfile->update(['profile_photo' => $path]);
            if (str_ends_with($path, 'shared.jpg')) {
                $other->businessProfile()->create(['brand_photo' => $path]);
            }
            app(BusinessPhotoService::class)->deleteProfilePhoto($provider->businessProfile);
            Storage::disk('public')->assertExists($path);
        }
    }
}
