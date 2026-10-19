<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProviderReviewLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function provider(): User
    {
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create();

        return $provider->load('businessProfile');
    }

    public function test_owner_can_edit_and_delete_reviews_and_rating_summary_tracks_every_change(): void
    {
        $provider = $this->provider();
        $first = User::factory()->customer()->create();
        $second = User::factory()->customer()->create();
        $url = route('providers.reviews.index', $provider);

        foreach ([[$first, 5], [$second, 3]] as [$reviewer, $rating]) {
            $this->actingAs($reviewer)->from($url)
                ->post(route('providers.reviews.store', $provider), ['rating' => $rating, 'comment' => 'Original comment'])
                ->assertRedirect($url)->assertSessionHasNoErrors();
        }
        $review = $provider->businessProfile->reviews()->where('reviewer_id', $first->id)->firstOrFail();
        $this->actingAs($first)->from($url)
            ->put(route('providers.reviews.update', [$provider, $review]), ['rating' => 1, 'comment' => 'Updated comment'])
            ->assertRedirect($url)->assertSessionHas('status', 'Your review has been updated.');
        $this->assertSame('Updated comment', $review->refresh()->comment);
        $this->assertSame(1, $review->rating);
        $this->assertSame('2.00', $provider->businessProfile->refresh()->average_rating);
        $this->assertSame(2, $provider->businessProfile->reviews_count);

        $this->delete(route('providers.reviews.destroy', [$provider, $review]))
            ->assertRedirect($url)->assertSessionHas('status', 'Your review has been deleted.');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertSame('3.00', $provider->businessProfile->refresh()->average_rating);
        $this->assertSame(1, $provider->businessProfile->reviews_count);

        $remaining = $provider->businessProfile->reviews()->firstOrFail();
        $this->actingAs($second)->delete(route('providers.reviews.destroy', [$provider, $remaining]))->assertRedirect();
        $this->assertSame('0.00', $provider->businessProfile->refresh()->average_rating);
        $this->assertSame(0, $provider->businessProfile->reviews_count);
    }

    public static function invalidReviews(): array
    {
        return [
            'missing rating' => [[], 'rating'],
            'zero rating' => [['rating' => 0], 'rating'],
            'rating above five' => [['rating' => 6], 'rating'],
            'fractional rating' => [['rating' => 2.5], 'rating'],
            'non numeric rating' => [['rating' => 'excellent'], 'rating'],
            'comment too long' => [['rating' => 5, 'comment' => str_repeat('x', 2001)], 'comment'],
            'non string comment' => [['rating' => 5, 'comment' => ['invalid']], 'comment'],
        ];
    }

    #[DataProvider('invalidReviews')]
    public function test_invalid_review_creation_and_edit_leave_reviews_and_summaries_unchanged(array $payload, string $field): void
    {
        $provider = $this->provider();
        $reviewer = User::factory()->customer()->create();
        $this->actingAs($reviewer)->postJson(route('providers.reviews.store', $provider), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('reviews', 0);

        $this->post(route('providers.reviews.store', $provider), ['rating' => 4, 'comment' => 'Keep this'])
            ->assertSessionHasNoErrors();
        $review = $provider->businessProfile->reviews()->firstOrFail();
        $this->putJson(route('providers.reviews.update', [$provider, $review]), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertSame(4, $review->refresh()->rating);
        $this->assertSame('Keep this', $review->comment);
        $this->assertSame('4.00', $provider->businessProfile->refresh()->average_rating);
        $this->assertSame(1, $provider->businessProfile->reviews_count);
    }

    public function test_owned_review_cannot_be_edited_or_deleted_through_another_provider_url(): void
    {
        $provider = $this->provider();
        $other = $this->provider();
        $reviewer = User::factory()->customer()->create();
        $this->actingAs($reviewer)->post(route('providers.reviews.store', $provider), ['rating' => 5])->assertSessionHasNoErrors();
        $review = $provider->businessProfile->reviews()->firstOrFail();

        $this->put(route('providers.reviews.update', [$other, $review]), ['rating' => 1])->assertNotFound();
        $this->delete(route('providers.reviews.destroy', [$other, $review]))->assertNotFound();
        $this->assertSame(5, $review->refresh()->rating);
        $this->assertSame('5.00', $provider->businessProfile->refresh()->average_rating);
        $this->assertSame(0, $other->businessProfile->refresh()->reviews_count);
    }

    public function test_public_reviews_paginate_newest_first_and_escape_review_content(): void
    {
        $provider = $this->provider();
        $reviews = Review::factory()->count(11)->create([
            'business_profile_id' => $provider->businessProfile->id,
            'comment' => '<script>alert("review")</script>',
            'created_at' => now(),
        ]);
        $first = $this->get(route('providers.reviews.index', $provider))
            ->assertOk()->assertSee('<script>alert("review")</script>')->assertDontSee('<script>alert("review")</script>', false);
        $this->assertSame($reviews->reverse()->take(10)->pluck('id')->values()->all(), $first->viewData('reviews')->pluck('id')->all());
        $second = $this->get(route('providers.reviews.index', [$provider, 'page' => 2]))->assertOk();
        $this->assertSame([$reviews->first()->id], $second->viewData('reviews')->pluck('id')->all());
    }
}
