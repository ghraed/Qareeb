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
        $this->actingAs($user)->get(route('business.dashboard'))->assertOk()->assertSee('0% complete')->assertSee('Mark availability');
        $this->assertDatabaseHas('business_profiles', ['user_id' => $user->id, 'is_available_for_hire' => false]);
        $this->get(route('business.profile.edit'))->assertOk();
        $this->assertSame(1, $user->businessProfile()->count());
    }

    public function test_next_steps_only_link_to_missing_details_and_disappear_when_completed(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $profile = $user->businessProfile()->create(['business_name' => 'Services', 'is_available_for_hire' => false]);

        $this->actingAs($user)->get(route('business.dashboard'))->assertOk()
            ->assertSee('Next steps')->assertDontSee('Add your business name')
            ->assertSee('Add your phone number')->assertSee('Add your bio')
            ->assertSee(route('business.profile.edit').'#phone', false)
            ->assertSee(route('business.profile.edit').'#description', false)
            ->assertDontSee('Configure availability');
        $this->get(route('business.profile.edit'))->assertOk()
            ->assertSee('Next steps')->assertDontSee('Add your business name')
            ->assertSee('href="#phone"', false)->assertSee('href="#description"', false);

        $profile->update(['phone' => '+96170000000']);
        $this->get(route('business.dashboard'))->assertOk()
            ->assertDontSee('Add your phone number')->assertSee('Add your bio');

        $profile->update([
            'description' => 'Experienced local services.', 'categories' => ['Photography'],
            'address' => 'Street', 'state' => ['Beirut'], 'city' => ['Achrafieh'],
            'profile_photo' => "businesses/{$user->id}/profile/one.jpg",
            'brand_photo' => "businesses/{$user->id}/brand/two.jpg",
        ]);
        $this->get(route('business.dashboard'))->assertOk()->assertDontSee('Next steps');
        $this->get(route('business.profile.edit'))->assertOk()->assertDontSee('Next steps');

        foreach (\App\Models\BusinessProfile::COMPLETION_FIELDS as $field => $label) {
            $original = $profile->{$field};
            $profile->update([$field => null]);
            $this->assertSame($field === 'state' ? 78 : 89, $profile->refresh()->completionPercentage());
            $this->get(route('business.dashboard'))->assertOk()->assertSee('Next steps')
                ->assertSee(route('business.profile.edit').'#'.$field, false);
            $this->get(route('business.profile.edit'))->assertOk()->assertSee('Next steps')
                ->assertSee('href="#'.$field.'"', false);
            $profile->update([$field => $original]);
        }
    }
}
