<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_state_logs_in_with_remember_and_reaches_its_authorized_destination(): void
    {
        foreach (AccountStatus::cases() as $status) {
            $provider = User::factory()->serviceProvider()->create(['account_status' => $status, 'password' => 'password']);
            $provider->businessProfile()->create();
            $this->withSession(['url.intended' => 'https://example.com/unsafe']);
            $before = session()->getId();
            $destination = $status === AccountStatus::Active ? 'business.dashboard' : 'business.account.status';
            $this->post(route('business.login.store'), ['email' => $provider->email, 'password' => 'password', 'remember' => '1'])
                ->assertRedirect(route($destination))->assertSessionMissing('url.intended');
            $this->assertNotSame($before, session()->getId());
            $this->assertAuthenticatedAs($provider);
            $this->assertNotEmpty($provider->refresh()->remember_token);
            $this->get(route($destination))->assertOk();
            $this->get(route('admin.dashboard'))->assertForbidden();
            if ($status !== AccountStatus::Active) {
                $this->get(route('business.dashboard'))->assertRedirect(route('business.account.status'));
                $this->put(route('business.profile.update'), ['business_name' => 'Unauthorized'])->assertRedirect(route('business.account.status'));
                $this->patch(route('business.profile.hiring-availability'), ['is_available_for_hire' => true])->assertRedirect(route('business.account.status'));
                $this->post(route('business.profile.profile-photo.store'))->assertRedirect(route('business.account.status'));
                $this->delete(route('business.profile.brand-photo.destroy'))->assertRedirect(route('business.account.status'));
                $this->assertNull($provider->businessProfile->refresh()->business_name);
            }
            $this->post(route('business.logout'))->assertRedirect(route('business.login'));
            $this->assertGuest();
            $this->get(route('business.dashboard'))->assertRedirect(route('business.login'));
        }
    }
}
