<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_creation_and_relationships_exist(): void
    {
        $provider = $this->createProvider();

        $this->assertInstanceOf(BusinessProfile::class, $provider->businessProfile);
        $this->assertTrue($provider->businessProfile->user->is($provider));
        $this->assertTrue($provider->businessProfile->user_id === $provider->id);
    }

    public function test_photo_upload_stores_file_and_updates_profile(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->post(route('business.profile.profile-photo.store'), [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect();

        $path = $provider->fresh()->businessProfile->profile_photo;

        $this->assertNotNull($path);
        $this->assertStringStartsWith("businesses/{$provider->id}/profile/", $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_photo_replacement_deletes_previous_file(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $this->actingAs($provider)->post(route('business.profile.profile-photo.store'), [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $oldPath = $provider->fresh()->businessProfile->profile_photo;

        $this->actingAs($provider)->post(route('business.profile.profile-photo.store'), [
            'photo' => UploadedFile::fake()->image('avatar-2.jpg'),
        ]);

        $newPath = $provider->fresh()->businessProfile->profile_photo;

        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_photo_deletion_clears_database_path(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $this->actingAs($provider)->post(route('business.profile.profile-photo.store'), [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $path = $provider->fresh()->businessProfile->profile_photo;

        $response = $this->actingAs($provider)->delete(route('business.profile.profile-photo.destroy'));

        $response->assertRedirect();
        $this->assertNull($provider->fresh()->businessProfile->profile_photo);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_invalid_photo_is_rejected(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $response = $this->from(route('business.profile.show'))
            ->actingAs($provider)
            ->post(route('business.profile.profile-photo.store'), [
                'photo' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ]);

        $response->assertRedirect(route('business.profile.show'));
        $response->assertSessionHasErrors('photo');
    }

    public function test_work_sample_upload_stores_image_and_appends_gallery(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->post(route('business.profile.work-samples.store'), [
            'photos' => [UploadedFile::fake()->image('work-sample.jpg')],
        ]);

        $response->assertRedirect();

        $samples = $provider->fresh()->businessProfile->work_samples;

        $this->assertCount(1, $samples);
        $this->assertStringStartsWith("businesses/{$provider->id}/work-samples/", $samples[0]);
        Storage::disk('public')->assertExists($samples[0]);
    }

    public function test_work_sample_deletion_removes_only_selected_image(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $this->actingAs($provider)->post(route('business.profile.work-samples.store'), [
            'photos' => [UploadedFile::fake()->image('one.jpg')],
        ]);
        $this->actingAs($provider)->post(route('business.profile.work-samples.store'), [
            'photos' => [UploadedFile::fake()->image('two.jpg')],
        ]);

        $samples = $provider->fresh()->businessProfile->work_samples;
        $firstPath = $samples[0];
        $secondPath = $samples[1];

        $response = $this->actingAs($provider)->delete(route('business.profile.work-samples.destroy', 0));

        $response->assertRedirect();
        $this->assertSame([$secondPath], $provider->fresh()->businessProfile->work_samples);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    public function test_work_sample_upload_rejects_non_images(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $response = $this->from(route('business.profile.show'))
            ->actingAs($provider)
            ->post(route('business.profile.work-samples.store'), [
                'photos' => [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')],
            ]);

        $response->assertRedirect(route('business.profile.show'));
        $response->assertSessionHasErrors('photos.0');
        $this->assertSame([], $provider->fresh()->businessProfile->work_samples);
    }

    public function test_work_sample_upload_accepts_multiple_images_in_one_request(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->post(route('business.profile.work-samples.store'), [
            'photos' => [
                UploadedFile::fake()->image('sample-1.jpg'),
                UploadedFile::fake()->image('sample-2.jpg'),
                UploadedFile::fake()->image('sample-3.jpg'),
            ],
        ]);

        $response->assertRedirect();
        $this->assertCount(3, $provider->fresh()->businessProfile->work_samples);
    }

    public function test_work_sample_upload_rejects_selecting_more_than_remaining_slots(): void
    {
        Storage::fake('public');
        $provider = $this->createProvider();

        for ($index = 0; $index < 4; $index++) {
            $this->actingAs($provider)->post(route('business.profile.work-samples.store'), [
                'photos' => [UploadedFile::fake()->image("sample-{$index}.jpg")],
            ]);
        }

        $response = $this->from(route('business.profile.show'))
            ->actingAs($provider)
            ->post(route('business.profile.work-samples.store'), [
                'photos' => [
                    UploadedFile::fake()->image('overflow-1.jpg'),
                    UploadedFile::fake()->image('overflow-2.jpg'),
                    UploadedFile::fake()->image('overflow-3.jpg'),
                ],
            ]);

        $response->assertRedirect(route('business.profile.show'));
        $response->assertSessionHasErrors('photos');
        $this->assertCount(4, $provider->fresh()->businessProfile->work_samples);
    }

    public function test_invalid_district_is_rejected(): void
    {
        $provider = $this->createProvider();

        $response = $this->from(route('business.profile.edit'))
            ->actingAs($provider)
            ->put(route('business.profile.update'), [
                'business_name' => 'Qareeb Works',
                'state' => ['Invalid District'],
                'is_available_for_hire' => 1,
            ]);

        $response->assertRedirect(route('business.profile.edit'));
        $response->assertSessionHasErrors(['state.0']);
    }

    public function test_city_outside_selected_districts_is_rejected(): void
    {
        $provider = $this->createProvider();

        $response = $this->from(route('business.profile.edit'))
            ->actingAs($provider)
            ->put(route('business.profile.update'), [
                'state' => ['Beirut'],
                'city' => ['Jounieh'],
                'is_available_for_hire' => 1,
            ]);

        $response->assertRedirect(route('business.profile.edit'));
        $response->assertSessionHasErrors(['city.0']);
    }

    public function test_profile_update_saves_multiple_districts_and_cities(): void
    {
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->put(route('business.profile.update'), [
            'business_name' => 'Qareeb Works',
            'state' => ['Beirut', 'Keserwan'],
            'city' => ['Achrafieh', 'Jounieh'],
            'is_available_for_hire' => 1,
        ]);

        $response->assertRedirect(route('business.profile.edit'));

        $provider->businessProfile->refresh();

        $this->assertSame(['Beirut', 'Keserwan'], $provider->businessProfile->state);
        $this->assertSame(['Achrafieh', 'Jounieh'], $provider->businessProfile->city);
    }

    public function test_profile_update_saves_selected_work_categories(): void
    {
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->put(route('business.profile.update'), [
            'business_name' => 'Qareeb Works',
            'categories' => ['Electrician', 'Technical & Repair Services'],
        ]);

        $response->assertRedirect(route('business.profile.edit'));

        $provider->businessProfile->refresh();

        $this->assertSame(['Electrician', 'Technical & Repair Services'], $provider->businessProfile->categories);
    }

    public function test_profile_update_uses_authenticated_user_email_for_business_email(): void
    {
        $provider = $this->createProvider();
        $provider->update(['email' => 'owner@example.com']);

        $response = $this->actingAs($provider)->put(route('business.profile.update'), [
            'business_name' => 'Qareeb Works',
            'business_email' => 'spoofed@example.com',
            'is_available_for_hire' => 1,
        ]);

        $response->assertRedirect(route('business.profile.edit'));

        $provider->businessProfile->refresh();

        $this->assertSame('owner@example.com', $provider->businessProfile->business_email);
    }

    public function test_all_cities_option_saves_all_cities_for_selected_districts(): void
    {
        $provider = $this->createProvider();

        $response = $this->actingAs($provider)->put(route('business.profile.update'), [
            'state' => ['Beirut'],
            'city' => ['__all__'],
            'is_available_for_hire' => 1,
        ]);

        $response->assertRedirect(route('business.profile.edit'));

        $provider->businessProfile->refresh();

        $this->assertSame(BusinessProfile::LEBANON_DISTRICT_CITIES['Beirut'], $provider->businessProfile->city);
    }

    public function test_review_creation_recalculates_rating_fields(): void
    {
        $provider = $this->createProvider();
        $reviewer = User::factory()->customer()->create();

        $response = $this->actingAs($reviewer)->post(route('providers.reviews.store', $provider), [
            'rating' => 4,
            'comment' => 'Very solid service.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'business_profile_id' => $provider->businessProfile->id,
            'reviewer_id' => $reviewer->id,
            'rating' => 4,
        ]);

        $provider->businessProfile->refresh();
        $this->assertSame('4.00', $provider->businessProfile->average_rating);
        $this->assertSame(1, $provider->businessProfile->reviews_count);
    }

    public function test_self_review_is_rejected(): void
    {
        $provider = $this->createProvider();

        $response = $this->from(route('providers.reviews.index', $provider))
            ->actingAs($provider)
            ->post(route('providers.reviews.store', $provider), [
                'rating' => 5,
                'comment' => 'My own review',
            ]);

        $response->assertRedirect(route('providers.reviews.index', $provider));
        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_duplicate_review_is_rejected(): void
    {
        $provider = $this->createProvider();
        $reviewer = User::factory()->customer()->create();

        Review::factory()->create([
            'business_profile_id' => $provider->businessProfile->id,
            'reviewer_id' => $reviewer->id,
            'rating' => 5,
        ]);

        $response = $this->from(route('providers.reviews.index', $provider))
            ->actingAs($reviewer)
            ->post(route('providers.reviews.store', $provider), [
                'rating' => 4,
                'comment' => 'Second review',
            ]);

        $response->assertRedirect(route('providers.reviews.index', $provider));
        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_review_update_authorization_is_enforced(): void
    {
        $provider = $this->createProvider();
        $owner = User::factory()->customer()->create();
        $intruder = User::factory()->customer()->create();

        $review = Review::factory()->create([
            'business_profile_id' => $provider->businessProfile->id,
            'reviewer_id' => $owner->id,
            'rating' => 4,
        ]);

        $response = $this->actingAs($intruder)->put(route('providers.reviews.update', [$provider, $review]), [
            'rating' => 2,
            'comment' => 'Unauthorized edit',
        ]);

        $response->assertForbidden();
    }

    public function test_review_delete_authorization_is_enforced(): void
    {
        $provider = $this->createProvider();
        $owner = User::factory()->customer()->create();
        $intruder = User::factory()->customer()->create();

        $review = Review::factory()->create([
            'business_profile_id' => $provider->businessProfile->id,
            'reviewer_id' => $owner->id,
            'rating' => 4,
        ]);

        $response = $this->actingAs($intruder)->delete(route('providers.reviews.destroy', [$provider, $review]));

        $response->assertForbidden();
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_correct_rating_average_and_review_count_are_maintained(): void
    {
        $provider = $this->createProvider();
        $first = User::factory()->customer()->create();
        $second = User::factory()->customer()->create();

        $this->actingAs($first)->post(route('providers.reviews.store', $provider), [
            'rating' => 5,
            'comment' => 'Excellent',
        ]);

        $this->actingAs($second)->post(route('providers.reviews.store', $provider), [
            'rating' => 3,
            'comment' => 'Good enough',
        ]);

        $provider->businessProfile->refresh();
        $this->assertSame('4.00', $provider->businessProfile->average_rating);
        $this->assertSame(2, $provider->businessProfile->reviews_count);
    }

    public function test_public_profile_is_visible(): void
    {
        $provider = $this->createProvider();
        $provider->businessProfile->update([
            'business_name' => 'Qareeb Plumbing',
            'description' => 'Trusted plumbing nearby.',
        ]);

        $response = $this->get(route('providers.show', $provider));

        $response->assertOk();
        $response->assertSee('Qareeb Plumbing');
        $response->assertSee('Trusted plumbing nearby.');
    }

    public function test_public_profile_returns_404_for_non_provider_users(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->get(route('providers.show', $user));

        $response->assertNotFound();
    }

    public function test_public_profile_returns_404_when_provider_has_no_business_profile(): void
    {
        $provider = User::factory()->serviceProvider()->create();

        $response = $this->get(route('providers.show', $provider));

        $response->assertNotFound();
    }

    public function test_public_profile_shows_available_hiring_card(): void
    {
        $provider = $this->createProvider();
        $provider->businessProfile->update([
            'business_name' => 'Qareeb Electric',
            'city' => ['Achrafieh'],
            'state' => ['Beirut'],
            'is_available_for_hire' => true,
        ]);

        $response = $this->get(route('providers.show', $provider));

        $response->assertOk();
        $response->assertSee('Available for hire');
        $response->assertSee('Email provider');
        $response->assertDontSee('Hire Now');
        $response->assertSee('Districts: Beirut');
        $response->assertSee('Cities: Achrafieh');
    }

    public function test_public_profile_shows_unavailable_hiring_card(): void
    {
        $provider = $this->createProvider();
        $provider->businessProfile->update([
            'business_name' => 'Qareeb Electric',
            'is_available_for_hire' => false,
        ]);

        $response = $this->get(route('providers.show', $provider));

        $response->assertOk();
        $response->assertSee('Currently unavailable');
        $response->assertSee('Email provider');
        $response->assertDontSee('Hire Now');
    }

    public function test_public_profile_uses_public_meta_data(): void
    {
        $provider = $this->createProvider();
        $provider->businessProfile->update([
            'business_name' => 'Qareeb Electric',
            'description' => 'Modern electrical support for homes and offices.',
        ]);

        $response = $this->get(route('providers.show', $provider));

        $response->assertOk();
        $response->assertSee('<meta name="description" content="Modern electrical support for homes and offices.">', false);
        $response->assertSee('<meta property="og:title" content="Qareeb Electric">', false);
    }

    public function test_public_profile_uses_user_email_as_business_email(): void
    {
        $provider = $this->createProvider();
        $provider->update(['email' => 'owner@example.com']);
        $provider->businessProfile->update([
            'business_email' => 'public-business@example.com',
            'business_name' => 'Qareeb Electric',
        ]);

        $response = $this->get(route('providers.show', $provider));

        $response->assertOk();
        $response->assertSee('owner@example.com');
        $response->assertDontSee('public-business@example.com');
    }

    private function createProvider(): User
    {
        $provider = User::factory()->serviceProvider()->create([
            'name' => 'Provider One',
        ]);

        $provider->businessProfile()->create();

        return $provider->load('businessProfile');
    }
}
