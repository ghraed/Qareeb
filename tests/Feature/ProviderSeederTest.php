<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\BusinessProfile;
use App\Models\Review;
use App\Models\User;
use App\Services\PublicProviderImage;
use Database\Seeders\ProviderSeeder;
use Database\Seeders\ResetProviderAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use RuntimeException;
use Tests\TestCase;

class ProviderSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_preserves_requested_accounts_and_seekers_and_removes_provider_data(): void
    {
        Storage::fake('public');
        $rozer = User::factory()->serviceProvider()->create(['email' => 'rozer@qareeb.com']);
        $rozerProfile = $rozer->businessProfile()->create(['business_name' => 'Original business']);
        $admin = User::factory()->admin()->create(['email' => 'admin@qareeb.com']);
        $seeker = User::factory()->customer()->create();
        $removed = User::factory()->serviceProvider()->create();
        $noEmail = User::factory()->serviceProvider()->create(['email' => null, 'username' => 'old-provider']);
        $otherAdmin = User::factory()->admin()->create();
        $profile = $removed->businessProfile()->create();
        Review::factory()->create(['business_profile_id' => $profile->id, 'reviewer_id' => $seeker->id]);
        PersonalAccessToken::query()->forceCreate([
            'tokenable_type' => $removed->getMorphClass(), 'tokenable_id' => $removed->id,
            'name' => 'test', 'token' => hash('sha256', 'seed-test-token'), 'abilities' => ['*'],
        ]);
        Storage::disk('public')->put("businesses/{$removed->id}/work-samples/old.jpg", 'old');
        Storage::disk('public')->put("businesses/{$rozer->id}/profile/keep.jpg", 'keep');
        $password = $rozer->password;

        $this->seed(ResetProviderAccountsSeeder::class);

        $this->assertSame($password, $rozer->fresh()->password);
        $this->assertSame('Original business', $rozerProfile->fresh()->business_name);
        foreach ([$rozer, $admin, $seeker] as $user) {
            $this->assertModelExists($user);
        }
        foreach ([$removed, $noEmail, $otherAdmin, $profile] as $model) {
            $this->assertModelMissing($model);
        }
        $this->assertDatabaseCount('reviews', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        Storage::disk('public')->assertMissing("businesses/{$removed->id}/work-samples/old.jpg");
        Storage::disk('public')->assertExists("businesses/{$rozer->id}/profile/keep.jpg");
    }

    public function test_cleanup_aborts_without_deleting_anyone_when_a_retained_account_is_missing(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        try {
            $this->seed(ResetProviderAccountsSeeder::class);
            $this->fail('Expected the missing retained account to stop cleanup.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('rozer@qareeb.com', $exception->getMessage());
        }
        $this->assertModelExists($provider);
    }

    public function test_providers_have_valid_local_galleries_varied_coverage_and_consistent_reviews_without_duplicates(): void
    {
        Storage::fake('public');
        $rozer = User::factory()->serviceProvider()->create(['email' => 'rozer@qareeb.com']);
        $rozer->businessProfile()->create(['business_name' => 'Keep me', 'categories' => ['Plumber']]);
        $this->seed(ProviderSeeder::class);
        $ids = User::query()->where('email', 'like', 'demo.%@example.test')->pluck('id')->all();
        $files = Storage::disk('public')->allFiles();
        $reviewCount = Review::query()->count();
        $this->seed(ProviderSeeder::class);

        $this->assertSame($ids, User::query()->where('email', 'like', 'demo.%@example.test')->pluck('id')->all());
        $this->assertSame($files, Storage::disk('public')->allFiles());
        $this->assertSame($reviewCount, Review::query()->count());
        $this->assertSame('Keep me', $rozer->businessProfile->business_name);
        $this->assertSame(31, User::query()->where('role', UserRole::ServiceProvider)->count());
        $this->assertSame(0, BusinessProfile::query()->containingSelection('categories', 'Electrician')->count());

        foreach (['Photography', 'Gardening', 'Interior Design'] as $category) {
            $profiles = BusinessProfile::query()->containingSelection('categories', $category)->get();
            $this->assertCount(10, $profiles);
            $this->assertCount(10, $profiles->pluck('state')->map(fn ($value) => $value[0])->unique());
            $this->assertTrue($profiles->contains('is_available_for_hire', true));
            $this->assertTrue($profiles->contains('is_available_for_hire', false));
            $this->assertTrue($profiles->contains('reviews_count', 0));
            foreach ($profiles as $profile) {
                $this->assertSame(100, $profile->completionPercentage());
                $this->assertTrue($profile->user->hasActiveProviderAccount());
                $this->assertTrue(Hash::check(ProviderSeeder::PASSWORD, $profile->user->password));
                $this->assertSame(config('terms.version'), $profile->user->terms_version);
                $this->assertContains($profile->city[0], BusinessProfile::citiesForDistricts($profile->state));
                $this->assertGreaterThanOrEqual(2, count($profile->work_samples));
                $this->assertLessThanOrEqual(BusinessProfile::MAX_WORK_SAMPLES, count($profile->work_samples));
                foreach (['profile' => [$profile->profile_photo], 'brand' => [$profile->brand_photo], 'gallery' => $profile->work_samples] as $type => $paths) {
                    foreach ($paths as $path) {
                        $this->assertTrue(PublicProviderImage::isOwnedPath($profile, $type, $path));
                        Storage::disk('public')->assertExists($path);
                        $this->assertNotFalse(getimagesize(Storage::disk('public')->path($path)));
                    }
                }
                $this->assertSame($profile->reviews()->count(), $profile->reviews_count);
                $this->assertEquals(round((float) $profile->reviews()->avg('rating'), 2), (float) $profile->average_rating);
            }
        }
        $this->get(route('providers.index', ['category' => 'Photography']))->assertOk()->assertSee('10 providers found');
        $this->get(route('providers.index', ['category' => 'Electrician']))->assertOk()->assertSee('No providers match your filters');
    }
}
