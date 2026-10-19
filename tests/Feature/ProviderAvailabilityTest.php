<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_and_normal_profile_save_preserve_consistent_availability_in_all_views(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $profile = $user->businessProfile()->create(['is_available_for_hire' => false]);
        foreach ([true, false] as $available) {
            $this->actingAs($user->fresh())->patch(route('business.profile.hiring-availability'), ['is_available_for_hire' => $available, 'user_id' => 999])->assertSessionHasNoErrors()->assertSessionHas('status');
            $this->assertSame($available, $profile->refresh()->is_available_for_hire);
            $this->put(route('business.profile.update'), ['business_name' => 'Services'])->assertSessionHasNoErrors();
            $this->assertSame($available, $profile->refresh()->is_available_for_hire);
            $this->actingAs($user->fresh())->get(route('business.dashboard'))->assertOk()->assertSee($available ? 'Mark unavailable' : 'Mark availability');
            $this->get(route('business.profile.edit'))->assertOk()->assertSee($available ? 'Stop hiring' : 'Start hiring')
                ->assertSee('id="hiring-heading"', false)->assertDontSee('>Availability</h2>', false);
            $this->get(route('providers.show', $user))->assertOk()->assertSee($available ? 'Available for hire' : 'Currently unavailable');
        }
        $this->patch(route('business.profile.hiring-availability'), ['is_available_for_hire' => 'bad'])->assertSessionHasErrors('is_available_for_hire');
        $this->assertFalse($profile->refresh()->is_available_for_hire);
    }

    public function test_save_failure_shows_feedback_and_leaves_persisted_availability_unchanged(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $profile = $user->businessProfile()->create(['is_available_for_hire' => false]);
        BusinessProfile::saving(fn () => false);
        $this->actingAs($user)->from(route('business.dashboard'))->patch(route('business.profile.hiring-availability'), ['is_available_for_hire' => true])->assertSessionHasErrors('is_available_for_hire');
        $this->assertFalse($profile->refresh()->is_available_for_hire);
    }

    public function test_json_toggle_returns_saved_state_and_reports_failures_without_redirecting(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $profile = $user->businessProfile()->create(['is_available_for_hire' => false]);

        foreach ([true, false] as $available) {
            $this->actingAs($user)->patchJson(route('business.profile.hiring-availability'), ['is_available_for_hire' => $available])
                ->assertOk()->assertExactJson(['is_available_for_hire' => $available]);
            $this->assertSame($available, $profile->refresh()->is_available_for_hire);
        }

        $this->patchJson(route('business.profile.hiring-availability'), ['is_available_for_hire' => 'invalid'])
            ->assertUnprocessable()->assertJsonValidationErrors('is_available_for_hire');

        BusinessProfile::saving(fn () => false);
        $this->patchJson(route('business.profile.hiring-availability'), ['is_available_for_hire' => true])
            ->assertStatus(500)->assertJsonPath('message', 'Availability could not be saved. Please try again.');
        $this->assertFalse($profile->refresh()->is_available_for_hire);
    }
}
