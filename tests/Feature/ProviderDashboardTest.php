<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_persisted_identity_business_details_links_and_completion(): void
    {
        $user = User::factory()->serviceProvider()->create(['name' => 'Provider Name', 'username' => 'provider', 'email' => null]);
        $profile = $user->businessProfile()->create([
            'business_name' => 'Real Business', 'description' => 'Real description', 'categories' => ['Photography'],
            'phone' => '+96170000000', 'address' => 'Real street', 'state' => ['Beirut'], 'city' => ['Achrafieh'], 'is_available_for_hire' => true,
        ]);
        $this->actingAs($user)->get(route('business.dashboard'))->assertOk()->assertSee('Provider Name')->assertSee('Real Business')
            ->assertSee('Real description')->assertSee('Photography')->assertSee('+96170000000')->assertSee('Real street')
            ->assertSee($profile->completionPercentage().'% complete')->assertSee('Mark unavailable')
            ->assertSee(route('business.profile.edit'), false)->assertSee(route('providers.show', $user), false);
        $this->get(route('business.profile.edit'))->assertOk();
        $this->get(route('providers.show', $user))->assertOk()->assertSee('Real Business');
    }

    public function test_active_legacy_provider_without_profile_gets_safe_empty_own_profile(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $this->actingAs($user)->get(route('business.dashboard'))->assertOk()->assertSee('0% complete')->assertSee('Accept enquiries');
        $this->assertDatabaseHas('business_profiles', ['user_id' => $user->id, 'is_available_for_hire' => false]);
        $this->get(route('business.profile.edit'))->assertOk();
        $this->assertSame(1, $user->businessProfile()->count());
    }
}
