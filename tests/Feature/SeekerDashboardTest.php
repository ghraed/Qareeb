<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeekerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_owned_persisted_name_completion_and_working_destinations(): void
    {
        $user = User::factory()->customer()->create(['name' => 'Actual Seeker', 'email' => null, 'username' => 'seeker']);
        $this->actingAs($user)->get(route('seeker.dashboard'))->assertOk()->assertSee('Actual Seeker')->assertSee('25% complete')->assertSee('Missing: Phone, District, City.')
            ->assertSee(route('seeker.profile.edit'), false)->assertSee(route('providers.index'), false)
            ->assertSee(route('seeker.complaints.create'), false)->assertSee(route('seeker.complaints.index'), false)
            ->assertDontSee('booking')->assertDontSee('payment');
        foreach (['seeker.profile.edit', 'providers.index', 'seeker.complaints.create', 'seeker.complaints.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $user->update(['phone' => '+96170123456', 'district' => 'Beirut', 'city' => 'Achrafieh']);
        $this->actingAs($user->fresh())->get(route('seeker.dashboard'))->assertOk()->assertSee('100% complete')->assertSee('Your profile is complete.');
        foreach (['admin', 'serviceProvider'] as $state) {
            $this->actingAs(User::factory()->$state()->create())->get(route('seeker.dashboard'))->assertForbidden();
        }
    }
}
