<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function providerRoutes(): array
    {
        return [
            'dashboard' => ['GET', 'business.dashboard', []],
            'profile view' => ['GET', 'business.profile.show', []],
            'profile edit' => ['GET', 'business.profile.edit', []],
            'profile save' => ['PUT', 'business.profile.update', []],
            'availability' => ['PATCH', 'business.profile.hiring-availability', []],
            'profile photo upload' => ['POST', 'business.profile.profile-photo.store', []],
            'brand photo upload' => ['POST', 'business.profile.brand-photo.store', []],
            'gallery upload' => ['POST', 'business.profile.work-samples.store', []],
            'profile photo delete' => ['DELETE', 'business.profile.profile-photo.destroy', []],
            'brand photo delete' => ['DELETE', 'business.profile.brand-photo.destroy', []],
            'gallery delete' => ['DELETE', 'business.profile.work-samples.destroy', ['index' => 0]],
        ];
    }

    #[DataProvider('providerRoutes')]
    public function test_provider_features_require_login_correct_role_and_active_approval(string $method, string $route, array $parameters): void
    {
        $url = route($route, $parameters);
        $this->call($method, $url)->assertRedirect(route('business.login'));
        foreach ([User::factory()->customer()->create(), User::factory()->admin()->create()] as $user) {
            $this->actingAs($user)->call($method, $url)->assertForbidden();
        }
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create(['business_name' => 'Unchanged', 'is_available_for_hire' => false]);
        foreach ([AccountStatus::Pending, AccountStatus::Rejected, AccountStatus::Suspended] as $status) {
            $provider->update(['account_status' => $status]);
            $this->actingAs($provider)->call($method, $url)
                ->assertRedirect(route('business.account.status'));
            $this->assertSame('Unchanged', $provider->businessProfile->refresh()->business_name);
            $this->assertFalse($provider->businessProfile->is_available_for_hire);
        }
    }

    public static function privateRoutes(): array
    {
        return [
            'admin dashboard' => ['GET', 'admin.dashboard', [], 'admin'],
            'admin complaint list' => ['GET', 'admin.complaints.index', [], 'admin'],
            'admin image report list' => ['GET', 'admin.image-reports.index', [], 'admin'],
            'seeker dashboard' => ['GET', 'seeker.dashboard', [], 'seeker'],
            'seeker profile' => ['GET', 'seeker.profile.edit', [], 'seeker'],
            'seeker profile save' => ['PUT', 'seeker.profile.update', [], 'seeker'],
            'seeker complaint list' => ['GET', 'seeker.complaints.index', [], 'seeker'],
            'seeker complaint form' => ['GET', 'seeker.complaints.create', [], 'seeker'],
            'seeker complaint submit' => ['POST', 'seeker.complaints.store', [], 'seeker'],
        ];
    }

    #[DataProvider('privateRoutes')]
    public function test_private_features_redirect_guests_and_reject_other_roles(string $method, string $route, array $parameters, string $role): void
    {
        $url = route($route, $parameters);
        $this->call($method, $url)->assertRedirect(route($role.'.login'));
        $wrongRole = $role === 'admin' ? User::factory()->customer()->create() : User::factory()->admin()->create();
        foreach ([$wrongRole, User::factory()->serviceProvider()->create()] as $user) {
            $this->actingAs($user)->call($method, $url)->assertForbidden();
        }
    }

    public function test_api_requires_authentication_and_hides_password_tokens_and_private_contact_fields(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
        $user = User::factory()->customer()->create([
            'phone' => '+96170123456', 'district' => 'Beirut', 'city' => 'Achrafieh',
        ]);
        $response = $this->actingAs($user, 'web')->getJson('/api/user')
            ->assertOk()->assertJsonPath('id', $user->id);
        foreach (['password', 'remember_token', 'phone', 'district', 'city'] as $field) {
            $this->assertArrayNotHasKey($field, $response->json());
        }
    }

    public function test_health_endpoint_is_public(): void
    {
        $this->get('/up')->assertOk();
    }
}
