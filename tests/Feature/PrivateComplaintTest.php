<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\ReportStatus;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PrivateComplaintTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return [...['type' => 'Provider conduct', 'description' => 'Private details about a provider interaction.', 'submission_token' => (string) Str::uuid()], ...$overrides];
    }

    public function test_seeker_submission_status_and_admin_review_are_private_and_owned(): void
    {
        $seeker = User::factory()->customer()->create();
        $other = User::factory()->customer()->create();
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create();
        $this->actingAs($seeker)->get(route('seeker.complaints.create'))->assertOk();
        $this->post(route('seeker.complaints.store'), $this->data(['provider_id' => $provider->id, 'user_id' => $other->id, 'internal_notes' => 'forged', 'status' => 'resolved']))->assertSessionHasNoErrors();
        $complaint = Complaint::firstOrFail();
        $this->assertSame($seeker->id, $complaint->user_id);
        $this->assertSame(ReportStatus::Open, $complaint->status);
        $this->assertNull($complaint->internal_notes);
        $this->get(route('seeker.complaints.show', $complaint))->assertOk()->assertSee('Open')->assertSee('Private details');
        $this->actingAs($other)->get(route('seeker.complaints.show', $complaint))->assertNotFound();
        $this->get(route('seeker.complaints.index'))->assertOk()->assertDontSee('Provider conduct');
        $this->patch(route('admin.complaints.update', $complaint), ['status' => 'resolved'])->assertForbidden();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.complaints.index'))->assertOk()->assertSee('Private details');
        foreach (['in_review', 'resolved'] as $status) {
            $this->patch(route('admin.complaints.update', $complaint), ['status' => $status, 'internal_notes' => 'CONFIDENTIAL ADMIN NOTE'])->assertSessionHasNoErrors();
        }
        $this->assertSame(ReportStatus::Resolved, $complaint->refresh()->status);
        $this->assertSame($admin->id, $complaint->reviewed_by);
        $this->assertNotNull($complaint->reviewed_at);
        $this->assertArrayNotHasKey('internal_notes', $complaint->toArray());
        $this->actingAs($seeker)->get(route('seeker.complaints.show', $complaint))->assertOk()->assertSee('Resolved')->assertDontSee('CONFIDENTIAL ADMIN NOTE');
        $this->get(route('providers.show', $provider))->assertOk()->assertDontSee('Private details')->assertDontSee('CONFIDENTIAL ADMIN NOTE');
    }

    public function test_duplicate_token_and_recent_content_are_idempotent_and_references_are_validated(): void
    {
        $seeker = User::factory()->customer()->create();
        $data = $this->data();
        $this->actingAs($seeker)->post(route('seeker.complaints.store'), $data)->assertSessionHasNoErrors();
        $this->post(route('seeker.complaints.store'), $data)->assertSessionHasNoErrors();
        $this->post(route('seeker.complaints.store'), $this->data())->assertSessionHasNoErrors();
        $this->assertSame(1, Complaint::count());
        $hidden = User::factory()->serviceProvider()->create(['account_status' => AccountStatus::Suspended]);
        $hidden->businessProfile()->create();
        $this->post(route('seeker.complaints.store'), $this->data(['provider_id' => $hidden->id]))->assertSessionHasErrors('provider_id');
        $this->post(route('seeker.complaints.store'), $this->data(['type' => 'Invalid', 'description' => 'short', 'provider_id' => $seeker->id]))->assertSessionHasErrors(['type', 'description', 'provider_id']);
        $this->assertSame(1, Complaint::count());
    }

    public function test_admin_review_shows_success_validation_and_save_failure_toasts(): void
    {
        $seeker = User::factory()->customer()->create();
        $this->actingAs($seeker)->post(route('seeker.complaints.store'), $this->data());
        $complaint = Complaint::firstOrFail();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->from(route('admin.complaints.index'))
            ->patch(route('admin.complaints.update', $complaint), ['status' => 'in_review'])
            ->assertRedirect(route('admin.complaints.index'))
            ->assertSessionHas('status', 'Complaint review saved.');
        $this->get(route('admin.complaints.index'))->assertOk()
            ->assertSee('Complaint review saved.')->assertSee('bg-emerald-50', false)
            ->assertSee('z-[2147483647]', false);

        $this->from(route('admin.complaints.index'))
            ->patch(route('admin.complaints.update', $complaint), ['status' => 'invalid'])
            ->assertSessionHasErrors('status');
        $this->get(route('admin.complaints.index'))->assertOk()->assertSee('bg-rose-50', false);

        Complaint::saving(fn () => false);
        $this->from(route('admin.complaints.index'))
            ->patch(route('admin.complaints.update', $complaint), ['status' => 'resolved'])
            ->assertSessionHasErrors('review');
        $this->assertSame(ReportStatus::InReview, $complaint->refresh()->status);
        $this->get(route('admin.complaints.index'))->assertOk()
            ->assertSee('Complaint review could not be saved. Please try again.')
            ->assertSee('bg-rose-50', false);
    }

    public function test_rate_limit_and_non_seeker_submission_permissions(): void
    {
        $seeker = User::factory()->customer()->create();
        $this->actingAs($seeker);
        foreach (range(1, 5) as $number) {
            $this->post(route('seeker.complaints.store'), $this->data(['description' => 'Distinct complaint details '.$number]))->assertSessionHasNoErrors();
        }
        $this->post(route('seeker.complaints.store'), $this->data())->assertStatus(429);
        foreach (['admin', 'serviceProvider'] as $state) {
            $this->actingAs(User::factory()->$state()->create())->get(route('seeker.complaints.index'))->assertForbidden();
            $this->post(route('seeker.complaints.store'), $this->data())->assertForbidden();
        }
        $this->app['auth']->forgetGuards();
        $this->get(route('seeker.complaints.index'))->assertRedirect(route('seeker.login'));
    }
}
