<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_photography_is_offered_saved_and_publicly_rendered_from_central_categories(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create();
        $this->assertContains('Photography', BusinessProfile::WORK_CATEGORIES);
        $this->actingAs($provider)->get(route('business.profile.edit'))->assertOk()->assertSee('Photography');
        $this->put(route('business.profile.update'), ['categories' => ['Photography', 'Electrician']])->assertSessionHasNoErrors();
        $this->assertSame(['Photography', 'Electrician'], $provider->businessProfile->refresh()->categories);
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Photography, Electrician');
    }

    public function test_retired_category_migration_is_exact_idempotent_and_flags_empty_profiles(): void
    {
        $profiles = [];
        foreach ([['Home Maintenance', 'Photography'], ['Computer Repair', 'Mobile Repair'], ['Mobile Repair', 'Mobile Repair Plus'], ['Electrician']] as $categories) {
            $provider = User::factory()->serviceProvider()->create();
            $profiles[] = $provider->businessProfile()->create(['categories' => $categories]);
        }
        $migration = require database_path('migrations/2026_10_10_000002_remove_retired_provider_categories.php');
        $migration->up();
        $this->assertSame(['Photography'], $profiles[0]->refresh()->categories);
        $this->assertSame([], $profiles[1]->refresh()->categories);
        $this->assertSame(['Mobile Repair Plus'], $profiles[2]->refresh()->categories);
        $this->assertSame(['Electrician'], $profiles[3]->refresh()->categories);
        $migration->up();
        $this->assertSame(['Photography'], $profiles[0]->refresh()->categories);
        $this->assertSame([], $profiles[1]->refresh()->categories);
        $this->get(route('providers.show', $profiles[1]->user))->assertOk()->assertSee('No categories listed yet');
        $this->actingAs($profiles[1]->user)->get(route('business.profile.edit'))->assertOk()->assertSee('Choose at least one current work category');
        foreach (['Home Maintenance', 'Computer Repair', 'Mobile Repair'] as $category) {
            $this->put(route('business.profile.update'), ['categories' => [$category]])->assertSessionHasErrors('categories.0');
        }
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Needs work category correction');
    }

    public function test_transportation_rename_preserves_assignments_deduplicates_and_accepts_legacy_filters(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['categories' => ['Moving', 'Photography', 'Transportation']]);
        $migration = require database_path('migrations/2026_10_10_000003_rename_moving_to_transportation.php');
        $migration->up();
        $this->assertSame(['Transportation', 'Photography'], $profile->refresh()->categories);
        $migration->up();
        $this->assertSame(['Transportation', 'Photography'], $profile->refresh()->categories);
        $this->assertSame('Transportation', BusinessProfile::normalizeCategoryFilter('Moving'));
        $this->assertNotContains('Moving', BusinessProfile::WORK_CATEGORIES);
        $this->actingAs($provider)->put(route('business.profile.update'), ['categories' => ['Moving']])->assertSessionHasErrors('categories.0');
        $this->put(route('business.profile.update'), ['categories' => ['Transportation']])->assertSessionHasNoErrors();
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Transportation')->assertDontSee('Moving');
    }
}
