<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HiringBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_hiring_badge_matches_persisted_status_and_direct_contacts_work(): void
    {
        $user = User::factory()->serviceProvider()->create(['email' => 'work@example.com']);
        $profile = $user->businessProfile()->create(['phone' => '+96170123456', 'is_available_for_hire' => true]);
        $this->get(route('providers.show', $user))->assertOk()->assertSee('#Hiring')->assertSee('Available for work')
            ->assertSee('accepting enquiries for work')->assertSee('tel:+96170123456', false)->assertSee('mailto:work@example.com', false);
        $profile->update(['is_available_for_hire' => false]);
        $this->get(route('providers.show', $user))->assertOk()->assertDontSee('#Hiring')->assertSee('Currently unavailable');
    }

    public function test_purple_hiring_frame_is_public_only_when_enabled_and_editor_updates_live(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['is_available_for_hire' => true]);
        $this->get(route('providers.show', $provider))->assertOk()
            ->assertSee('#HIRING')->assertSee('stop-color="#6f2cff"', false);
        $this->actingAs($provider)->get(route('business.profile.edit'))->assertOk()
            ->assertSee('#HIRING')->assertSee('x-show="hiring"', false);

        $profile->update(['is_available_for_hire' => false]);
        $this->get(route('providers.show', $provider))->assertOk()->assertDontSee('#HIRING');
        $this->get(route('business.profile.edit'))->assertOk()
            ->assertSee('x-show="hiring"', false)
            ->assertSee('hiring = $event.detail.available', false);
    }
}
