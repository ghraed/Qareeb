<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderModerationTest extends TestCase
{
    use RefreshDatabase;

    private function provider(AccountStatus $status): User
    {
        $provider = User::factory()->serviceProvider()->create(['account_status' => $status]);
        $provider->businessProfile()->create(['business_name' => 'Services', 'is_available_for_hire' => true]);

        return $provider;
    }

    public function test_approval_rejection_suspension_and_reactivation_record_admin_and_reset_availability(): void
    {
        $admin = User::factory()->admin()->create();
        $provider = $this->provider(AccountStatus::Pending);
        $this->actingAs($admin)->patch(route('admin.providers.approve', $provider))->assertRedirect();
        $provider->refresh();
        $this->assertSame(AccountStatus::Active, $provider->account_status);
        $this->assertSame($admin->id, $provider->approved_by);
        $this->assertNotNull($provider->approved_at);
        $this->assertFalse($provider->businessProfile->is_available_for_hire);
        $this->patch(route('admin.providers.suspend', $provider), ['suspension_reason' => 'Review needed'])->assertRedirect();
        $this->assertSame($admin->id, $provider->refresh()->suspended_by);
        $this->assertNotNull($provider->suspended_at);
        $provider->businessProfile->update(['is_available_for_hire' => true]);
        $this->patch(route('admin.providers.activate', $provider))->assertRedirect();
        $this->assertSame(AccountStatus::Active, $provider->refresh()->account_status);
        $this->assertFalse($provider->businessProfile->is_available_for_hire);
        $this->assertNull($provider->suspension_reason);
        $this->patch(route('admin.providers.reject', $provider), [])->assertSessionHasErrors('rejection_reason');
        $this->patch(route('admin.providers.reject', $provider), ['rejection_reason' => 'Incomplete documents'])->assertRedirect();
        $provider->refresh();
        $this->assertSame(AccountStatus::Rejected, $provider->account_status);
        $this->assertSame($admin->id, $provider->rejected_by);
        $this->assertNotNull($provider->rejected_at);
        $this->assertSame('Incomplete documents', $provider->rejection_reason);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Incomplete documents');
        $this->actingAs($provider)->get(route('business.account.status'))->assertOk()->assertSee('Incomplete documents');
    }

    public function test_restricted_providers_are_hidden_from_public_profiles_and_all_review_actions(): void
    {
        $seeker = User::factory()->customer()->create();
        foreach ([AccountStatus::Pending, AccountStatus::Rejected, AccountStatus::Suspended] as $status) {
            $provider = $this->provider($status);
            $review = $provider->businessProfile->reviews()->create(['reviewer_id' => $seeker->id, 'rating' => 4]);
            $this->get(route('providers.show', $provider))->assertNotFound();
            $this->get(route('providers.reviews.index', $provider))->assertNotFound();
            $this->actingAs($seeker)->post(route('providers.reviews.store', $provider), ['rating' => 5])->assertNotFound();
            $this->put(route('providers.reviews.update', [$provider, $review]), ['rating' => 5])->assertNotFound();
            $this->delete(route('providers.reviews.destroy', [$provider, $review]))->assertNotFound();
            $this->actingAs($provider)->get(route('business.profile.edit'))->assertRedirect(route('business.account.status'));
        }
    }

    public function test_public_review_access_handles_non_providers_and_missing_profiles_and_roles_cannot_moderate(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create();
        $provider = User::factory()->serviceProvider()->create();
        foreach ([$admin, $customer, $provider] as $target) {
            $this->get(route('providers.reviews.index', $target))->assertNotFound();
            $this->actingAs($customer)->post(route('providers.reviews.store', $target), ['rating' => 5])->assertNotFound();
        }
        $pending = $this->provider(AccountStatus::Pending);
        foreach ([$customer, $provider] as $actor) {
            foreach (['approve', 'reject', 'suspend', 'activate'] as $action) {
                $this->actingAs($actor)->patch(route('admin.providers.'.$action, $pending), ['rejection_reason' => 'x', 'suspension_reason' => 'x'])->assertForbidden();
            }
        }
        $this->assertSame(AccountStatus::Pending, $pending->refresh()->account_status);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Services');
    }
}
