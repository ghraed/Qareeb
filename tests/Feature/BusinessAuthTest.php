<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_registration_creates_pending_account_and_logs_user_in(): void
    {
        $response = $this->post(route('business.register.store'), [
            'name' => 'Acme Provider',
            'email' => 'provider@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('business.account.status'));
        $response->assertSessionHas('status');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'provider@example.com',
            'role' => UserRole::ServiceProvider->value,
            'account_status' => AccountStatus::Pending->value,
        ]);
    }

    public function test_registration_always_assigns_service_provider_role(): void
    {
        $this->post(route('business.register.store'), [
            'name' => 'Acme Provider',
            'email' => 'provider@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Admin->value,
        ]);

        $user = User::query()->where('email', 'provider@example.com')->firstOrFail();

        $this->assertSame(UserRole::ServiceProvider, $user->role);
    }

    public function test_registration_creates_empty_business_profile(): void
    {
        $this->post(route('business.register.store'), [
            'name' => 'Acme Provider',
            'email' => 'provider@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'provider@example.com')->firstOrFail();

        $this->assertDatabaseHas('business_profiles', [
            'user_id' => $user->id,
            'business_name' => null,
            'business_email' => 'provider@example.com',
            'description' => null,
            'average_rating' => 0,
            'reviews_count' => 0,
            'is_available_for_hire' => false,
        ]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'provider@example.com',
        ]);

        $response = $this->from(route('business.register'))->post(route('business.register.store'), [
            'name' => 'Acme Provider',
            'email' => 'provider@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('business.register'));
        $response->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_is_required(): void
    {
        $response = $this->from(route('business.register'))->post(route('business.register.store'), [
            'name' => 'Acme Provider',
            'email' => 'provider@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertRedirect(route('business.register'));
        $response->assertSessionHasErrors('password');
    }

    public function test_service_provider_can_log_in_successfully(): void
    {
        $user = User::factory()->serviceProvider()->create([
            'email' => 'provider@example.com',
            'password' => 'password',
        ]);
        $user->businessProfile()->create();

        $response = $this->post(route('business.login.store'), [
            'email' => 'provider@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('business.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pending_service_provider_is_redirected_to_account_status_after_login(): void
    {
        $user = User::factory()->pendingProvider()->create([
            'email' => 'provider@example.com',
            'password' => 'password',
        ]);
        $user->businessProfile()->create();

        $response = $this->post(route('business.login.store'), [
            'email' => 'provider@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('business.account.status'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_is_rejected(): void
    {
        User::factory()->serviceProvider()->create([
            'email' => 'provider@example.com',
        ])->businessProfile()->create();

        $response = $this->from(route('business.login'))->post(route('business.login.store'), [
            'email' => 'provider@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('business.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_provider_login_is_rejected(): void
    {
        User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $response = $this->from(route('business.login'))->post(route('business.login.store'), [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('business.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_dashboard_requires_authenticated_service_provider(): void
    {
        $response = $this->get(route('business.dashboard'));

        $response->assertRedirect(route('business.login'));
    }

    public function test_pending_service_provider_cannot_access_dashboard(): void
    {
        $user = User::factory()->pendingProvider()->create();
        $user->businessProfile()->create();

        $response = $this->actingAs($user)->get(route('business.dashboard'));

        $response->assertRedirect(route('business.account.status'));
    }

    public function test_logout_ends_authenticated_session(): void
    {
        $user = User::factory()->serviceProvider()->create();
        $user->businessProfile()->create();

        $response = $this->actingAs($user)->post(route('business.logout'));

        $response->assertRedirect(route('business.login'));
        $this->assertGuest();
    }
}
