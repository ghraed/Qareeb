<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reaches_dashboard_from_both_portals_with_remember_and_safe_redirect(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'password']);
        foreach (['admin', 'business'] as $portal) {
            $this->withSession(['url.intended' => 'https://example.com/untrusted'])
                ->post(route($portal.'.login.store'), ['email' => $admin->email, 'password' => 'password', 'remember' => '1'])
                ->assertRedirect(route('admin.dashboard'))->assertSessionMissing('url.intended');
            $this->assertAuthenticatedAs($admin);
            $this->assertNotEmpty($admin->refresh()->remember_token);
            $this->get(route('admin.dashboard'))->assertOk();
            $this->get(route('business.dashboard'))->assertForbidden();
            $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
            $this->assertGuest();
        }
    }

    public function test_wrong_credentials_and_non_admins_cannot_enter_admin_portal(): void
    {
        foreach (['customer', 'serviceProvider'] as $state) {
            $user = User::factory()->$state()->create(['password' => 'password']);
            $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'password'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
            $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
            $this->app['auth']->forgetGuards();
        }
        $admin = User::factory()->admin()->create(['password' => 'password']);
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_login_rotates_session_and_logout_invalidates_it(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'password']);
        $this->withSession(['marker' => 'before']);
        $before = session()->getId();
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password']);
        $this->assertNotSame($before, session()->getId());
        $this->post(route('admin.logout'))->assertSessionMissing('marker');
        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }
}
