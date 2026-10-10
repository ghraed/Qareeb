<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectProviderContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_has_working_direct_contacts_without_booking_promises_in_both_states(): void
    {
        $user = User::factory()->serviceProvider()->create(['email' => 'contact@example.com']);
        $profile = $user->businessProfile()->create(['phone' => '+961 70 123 456']);
        foreach ([true, false] as $available) {
            $profile->update(['is_available_for_hire' => $available]);
            $this->get(route('providers.show', $user))->assertOk()->assertSee('href="tel:+96170123456"', false)
                ->assertSee('href="mailto:contact@example.com"', false)->assertDontSee('Hire Now')->assertDontSee('coming soon')
                ->assertDontSee('booking')->assertDontSee('request workflow');
        }
        $user->update(['email' => null, 'username' => 'contact']);
        $profile->update(['phone' => null]);
        $this->get(route('providers.show', $user))->assertOk()->assertSee('No public contact details')->assertDontSee('mailto:')->assertDontSee('tel:');
    }
}
