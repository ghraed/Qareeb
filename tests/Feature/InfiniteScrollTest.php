<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfiniteScrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_queues_load_independent_batches_and_keep_full_counts(): void
    {
        $admin = User::factory()->admin()->create();
        foreach ([AccountStatus::Pending, AccountStatus::Active, AccountStatus::Suspended] as $status) {
            foreach (range(1, 13) as $number) {
                User::factory()->serviceProvider()->create([
                    'name' => $status->value.' provider '.$number,
                    'account_status' => $status,
                ]);
            }
        }

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('13 awaiting review')
            ->assertSee('13 active')
            ->assertSee('pending_page=2', false)
            ->assertSee('active_page=2', false)
            ->assertSee('restricted_page=2', false)
            ->assertSee('pending provider 13')
            ->assertDontSee('pending provider 1<', false)
            ->assertDontSee('aria-label="Pagination Navigation"', false);

        $this->get(route('admin.dashboard', ['pending_page' => 2]))
            ->assertOk()
            ->assertSee('pending provider 1')
            ->assertDontSee('pending provider 13')
            ->assertSee('active provider 13')
            ->assertSee('suspended provider 13');
    }
}
