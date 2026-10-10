<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_persisted_valid_fields_reach_one_hundred_without_email_and_views_agree(): void
    {
        $user = User::factory()->serviceProvider()->create(['username' => 'complete', 'email' => null]);
        $profile = $user->businessProfile()->create();
        $this->assertSame(0, $profile->completionPercentage());
        $profile->update([
            'business_name' => 'Services', 'phone' => '+96170000000', 'description' => 'Helpful services',
            'categories' => ['Photography'], 'address' => 'Street', 'state' => ['Beirut'], 'city' => ['Achrafieh'],
            'profile_photo' => "businesses/{$user->id}/profile/one.jpg", 'brand_photo' => "businesses/{$user->id}/brand/two.jpg",
        ]);
        $this->assertSame(100, $profile->refresh()->completionPercentage());
        $this->assertSame([], $profile->missingCompletionFields());
        foreach (['business.dashboard', 'business.profile.edit'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk()->assertSee('100% complete')->assertSee('Your profile is complete.');
        }
        $profile->update(['phone' => null]);
        $this->assertSame(89, $profile->refresh()->completionPercentage());
        $this->assertSame(['Phone'], $profile->missingCompletionFields());
        foreach (['business.dashboard', 'business.profile.edit'] as $route) {
            $this->actingAs($user->fresh())->get(route($route))->assertOk()->assertSee('89% complete')->assertSee('Missing: Phone.');
        }
        $profile->update(['phone' => 'invalid', 'categories' => ['Removed label'], 'city' => ['Jounieh']]);
        $this->assertSame(67, $profile->refresh()->completionPercentage());
        $this->assertSame(['Phone', 'Work categories', 'Cities'], $profile->missingCompletionFields());
    }
}
