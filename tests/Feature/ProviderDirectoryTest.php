<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProviderDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function provider(string $name, array $categories, AccountStatus $status = AccountStatus::Active): User
    {
        $user = User::factory()->serviceProvider()->create(['account_status' => $status]);
        $user->businessProfile()->create(['business_name' => $name, 'categories' => $categories, 'state' => ['Beirut'], 'city' => ['Achrafieh'], 'is_available_for_hire' => true]);

        return $user;
    }

    public function test_public_directory_matches_exact_categories_and_excludes_every_restricted_or_non_provider_account(): void
    {
        $this->provider('Matching photography', ['Photography', 'Transportation']);
        $legacy = $this->provider('Legacy category', []);
        DB::table('business_profiles')->where('user_id', $legacy->id)->update(['categories' => 'Photography']);
        $this->provider('Wrong exact category', ['Wedding Photography']);
        foreach ([AccountStatus::Pending, AccountStatus::Rejected, AccountStatus::Suspended] as $status) {
            $this->provider('Hidden '.$status->value, ['Photography'], $status);
        }
        User::factory()->admin()->create()->businessProfile()->create(['business_name' => 'Hidden admin', 'categories' => ['Photography']]);
        User::factory()->customer()->create()->businessProfile()->create(['business_name' => 'Hidden seeker', 'categories' => ['Photography']]);
        $missing = User::factory()->serviceProvider()->create(['name' => 'Missing profile']);
        $response = $this->get(route('providers.index', ['category' => 'Photography']));
        $response->assertOk()->assertSee('Matching photography')->assertSee('Legacy category')->assertSee('2 providers found')->assertSee('#Hiring')
            ->assertDontSee('Wrong exact category')->assertDontSee('Hidden pending')->assertDontSee('Hidden rejected')->assertDontSee('Hidden suspended')->assertDontSee('Hidden admin')->assertDontSee('Hidden seeker')->assertDontSee($missing->name);
        $this->get(route('providers.index', ['category' => 'Moving']))->assertOk()->assertSee('Matching photography')->assertViewHas('filters', fn ($filters) => $filters['category'] === 'Transportation');
        $this->get(route('providers.index', ['category' => 'Computer Repair']))->assertRedirect(route('providers.index'))->assertSessionHasErrors('category');
        $this->get(route('providers.index', ['category' => ['Photography']]))->assertSessionHasErrors('category');
    }

    public function test_pagination_preserves_filter_and_cards_link_to_profiles_and_empty_state_is_clear(): void
    {
        foreach (range(1, 13) as $number) {
            $this->provider('Photo '.$number, ['Photography']);
        }
        $this->get(route('providers.index', ['category' => 'Photography']))->assertOk()->assertSee('Photo 1')->assertDontSee('Photo 13')->assertSee('category=Photography&amp;page=2', false);
        $this->get(route('providers.index', ['category' => 'Photography', 'page' => 2]))->assertOk()->assertSee('Photo 13')->assertDontSee('Photo 1<');
        $this->get(route('providers.index', ['category' => 'Electrician']))->assertOk()->assertSee('No providers match your filters');
        $this->get(route('providers.index'))->assertOk()->assertSee('Clear filters');
    }

    public function test_district_and_city_coverage_compose_with_categories_and_legacy_storage(): void
    {
        $match = $this->provider('Combined match', ['Photography']);
        $match->businessProfile->update(['state' => ['Beirut', 'Keserwan'], 'city' => ['Achrafieh', 'Jounieh']]);
        $legacy = $this->provider('Legacy coverage', ['Photography']);
        DB::table('business_profiles')->where('user_id', $legacy->id)->update(['state' => 'Beirut', 'city' => 'Achrafieh']);
        $differentCity = $this->provider('Different city', ['Photography']);
        $differentCity->businessProfile->update(['city' => ['Ras Beirut']]);
        $this->provider('Different category', ['Electrician']);
        $filters = ['category' => 'Photography', 'district' => 'Beirut', 'city' => 'Achrafieh'];
        $this->get(route('providers.index', $filters))->assertOk()->assertSee('Combined match')->assertSee('Legacy coverage')->assertSee('2 providers found')->assertDontSee('Different city')->assertDontSee('Different category');
        $this->get(route('providers.index', ['district' => 'Keserwan', 'city' => 'Jounieh']))->assertOk()->assertSee('Combined match')->assertSee('1 provider found');
        $this->get(route('providers.index', ['district' => 'Beirut', 'city' => 'Jounieh']))->assertSessionHasErrors('city');
        $this->get(route('providers.index', ['city' => 'Achrafieh']))->assertSessionHasErrors(['district', 'city']);
        $this->get(route('providers.index', ['district' => 'Unknown']))->assertSessionHasErrors('district');
        $this->get(route('providers.index', ['district' => ['Beirut']]))->assertSessionHasErrors('district');
        foreach (range(1, 12) as $number) {
            $this->provider('Extra match '.$number, ['Photography']);
        }
        $response = $this->get(route('providers.index', $filters));
        $response->assertOk()->assertSee('district=Beirut', false)->assertSee('city=Achrafieh', false)->assertSee('category=Photography', false)->assertSee('page=2', false);
    }
}
