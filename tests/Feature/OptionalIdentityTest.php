<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class OptionalIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_less_provider_registers_and_logs_in_with_normalized_username(): void
    {
        $this->post(route('business.register.store'), [
            'name' => 'Provider', 'username' => '  PHOTO.Pro  ',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertRedirect(route('business.account.status'))->assertSessionHasNoErrors();
        $user = User::where('username', 'photo.pro')->firstOrFail();
        $this->assertNull($user->email);
        $this->assertNull($user->businessProfile->business_email);
        $this->post(route('business.logout'));
        $this->post(route('business.login.store'), ['email' => ' PHOTO.PRO ', 'password' => 'password'])
            ->assertRedirect(route('business.account.status'))->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_shared_identity_rules_support_email_less_customers_without_business_profiles(): void
    {
        $data = ['username' => 'seeker.one', 'email' => null];
        $this->assertFalse(Validator::make($data, AccountIdentity::registrationRules())->fails());
        $user = User::factory()->customer()->create([...$data, 'password' => 'password']);
        $this->assertTrue(AccountIdentity::authenticate('SEEKER.ONE', 'password')->is($user));
        $this->assertNull($user->businessProfile);
    }

    public function test_identity_is_required_and_duplicates_and_email_shaped_usernames_are_rejected(): void
    {
        User::factory()->create(['username' => 'provider', 'email' => 'LEGACY@example.com']);
        foreach ([
            [[], 'username'],
            [['username' => 'PROVIDER'], 'username'],
            [['email' => 'legacy@EXAMPLE.com'], 'email'],
            [['username' => 'legacy@example.com'], 'username'],
            [['username' => ['unexpected']], 'username'],
            [['email' => ['unexpected'], 'username' => 'valid'], 'email'],
        ] as [$identity, $error]) {
            $this->post(route('business.register.store'), [
                'name' => 'Provider', ...$identity,
                'password' => 'password', 'password_confirmation' => 'password',
            ])->assertSessionHasErrors($error);
        }
    }

    public function test_legacy_email_login_and_username_admin_login_work(): void
    {
        $provider = User::factory()->serviceProvider()->create(['email' => 'legacy@example.com', 'password' => 'password']);
        $this->post(route('business.login.store'), ['email' => ' LEGACY@EXAMPLE.COM ', 'password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($provider);
        $this->post(route('business.logout'));
        $admin = User::factory()->create(['email' => null, 'username' => 'admin', 'role' => UserRole::Admin, 'password' => 'password']);
        $this->post(route('admin.login.store'), ['email' => 'ADMIN', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_email_less_public_profile_and_private_views_render_without_empty_email_links(): void
    {
        $user = User::factory()->serviceProvider()->create(['username' => 'provider', 'email' => null]);
        $user->businessProfile()->create(['business_name' => 'Provider']);
        $this->get(route('providers.show', $user))->assertOk()->assertSee('Not publicly listed')->assertDontSee('https://mail.google.com');
        $this->actingAs($user)->get(route('business.dashboard'))->assertOk()->assertSee('provider');
        $this->get(route('business.profile.edit'))->assertOk();
    }

    public function test_email_is_excluded_from_completion(): void
    {
        $profile = new BusinessProfile([
            'business_name' => 'Provider', 'phone' => '+96170000000', 'description' => 'Services',
            'categories' => ['Electrician'], 'address' => 'Street', 'state' => ['Beirut'],
            'city' => ['Achrafieh'], 'profile_photo' => 'profile.png', 'brand_photo' => 'brand.png',
        ]);
        $this->assertSame(100, $profile->completionPercentage());
        $profile->business_email = 'optional@example.com';
        $this->assertSame(100, $profile->completionPercentage());
    }
}
