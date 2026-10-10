<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderPreregistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_stays_private_until_approval_then_same_account_can_complete_profile(): void
    {
        $this->get(route('business.register'))->assertOk()->assertSee('administrator review');
        $this->post(route('business.register.store'), [
            'name' => 'Early Provider', 'username' => 'early.provider',
            'password' => 'password', 'password_confirmation' => 'password',
            'terms_accepted' => true, 'terms_version' => config('terms.version'),
            'role' => 'admin', 'account_status' => 'active',
        ])->assertRedirect(route('business.account.status'));
        $provider = User::where('username', 'early.provider')->firstOrFail();
        $this->assertTrue($provider->isServiceProvider());
        $this->assertSame(AccountStatus::Pending, $provider->account_status);
        $this->assertNotNull($provider->businessProfile);
        $this->assertFalse($provider->businessProfile->is_available_for_hire);
        $this->get(route('business.account.status'))->assertOk()->assertSee('Check account status')->assertSee('After approval');
        $this->get(route('business.profile.edit'))->assertRedirect(route('business.account.status'));
        $this->get(route('providers.show', $provider))->assertNotFound();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch(route('admin.providers.approve', $provider))->assertRedirect();
        $this->actingAs($provider->refresh())->get(route('business.account.status'))->assertRedirect(route('business.dashboard'));
        $this->get(route('business.profile.edit'))->assertOk();
        $this->put(route('business.profile.update'), ['business_name' => 'My Services', 'categories' => ['Electrician']])->assertSessionHasNoErrors();
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('My Services');
    }
}
