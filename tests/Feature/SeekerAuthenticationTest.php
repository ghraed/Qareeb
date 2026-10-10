<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeekerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_less_seeker_registers_with_safe_role_and_no_business_profile_and_logs_in_out(): void
    {
        $this->get(route('seeker.register'))->assertOk();
        $this->get(route('seeker.login'))->assertOk();
        $this->post(route('seeker.register.store'), ['name' => 'Real Seeker', 'username' => ' SEEKER.One ',
            'password' => 'password', 'terms_accepted' => true, 'terms_version' => config('terms.version'), 'password_confirmation' => 'password', 'role' => 'admin'])
            ->assertRedirect(route('seeker.dashboard'))->assertSessionHasNoErrors();
        $seeker = User::where('username', 'seeker.one')->firstOrFail();
        $this->assertSame(UserRole::Customer, $seeker->role);
        $this->assertNull($seeker->email);
        $this->assertNull($seeker->businessProfile);
        $this->get(route('seeker.dashboard'))->assertOk()->assertSee('Real Seeker');
        $this->get(route('business.dashboard'))->assertForbidden();
        $this->get(route('business.profile.edit'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->post(route('seeker.logout'))->assertRedirect(route('seeker.login'));
        $this->assertGuest();
        $this->get(route('seeker.dashboard'))->assertRedirect(route('seeker.login'));
        $this->withSession(['url.intended' => route('admin.dashboard')]);
        $before = session()->getId();
        $this->post(route('seeker.login.store'), ['email' => 'SEEKER.ONE', 'password' => 'password', 'remember' => '1'])
            ->assertRedirect(route('seeker.dashboard'))->assertSessionMissing('url.intended');
        $this->assertNotSame($before, session()->getId());
        $this->assertAuthenticatedAs($seeker);
        $this->assertNotEmpty($seeker->refresh()->remember_token);
    }

    public function test_legacy_seeker_email_login_and_invalid_or_wrong_role_credentials(): void
    {
        $seeker = User::factory()->customer()->create(['email' => 'seeker@example.com', 'password' => 'password']);
        $this->post(route('seeker.login.store'), ['email' => 'SEEKER@EXAMPLE.COM', 'password' => 'password'])->assertRedirect(route('seeker.dashboard'));
        $this->get(route('business.login'))->assertRedirect(route('seeker.dashboard'));
        $this->post(route('seeker.logout'));
        $this->post(route('seeker.login.store'), ['email' => $seeker->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        foreach (['admin', 'serviceProvider'] as $state) {
            $user = User::factory()->$state()->create(['password' => 'password']);
            $this->post(route('seeker.login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
            $this->assertGuest();
            $this->actingAs($user)->get(route('seeker.dashboard'))->assertForbidden();
            $this->app['auth']->forgetGuards();
        }
    }

    public function test_registration_enforces_shared_identity_uniqueness_and_confirmation(): void
    {
        User::factory()->create(['username' => 'taken']);
        $this->post(route('seeker.register.store'), ['name' => 'Seeker', 'username' => 'TAKEN', 'password' => 'password', 'terms_accepted' => true, 'terms_version' => config('terms.version'), 'password_confirmation' => 'different'])->assertSessionHasErrors(['username', 'password']);
        $this->post(route('seeker.register.store'), ['name' => 'Seeker', 'password' => 'password', 'terms_accepted' => true, 'terms_version' => config('terms.version'), 'password_confirmation' => 'password'])->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
