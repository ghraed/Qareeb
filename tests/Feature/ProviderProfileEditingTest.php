<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderProfileEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_profile_updates_are_owned_public_and_do_not_change_identity(): void
    {
        $provider = User::factory()->serviceProvider()->create(['username' => 'owner', 'email' => null]);
        $profile = $provider->businessProfile()->create();
        $other = User::factory()->serviceProvider()->create();
        $otherProfile = $other->businessProfile()->create(['business_name' => 'Other business']);
        $this->actingAs($provider)->put(route('business.profile.update'), [
            'user_id' => $other->id, 'profile_id' => $otherProfile->id, 'email' => 'changed@example.com', 'username' => 'changed',
            'business_name' => 'Owned services', 'phone' => '+961 70 123 456', 'description' => 'Skilled services',
            'categories' => ['Photography', 'Transportation'], 'state' => ['Beirut', 'Keserwan'],
            'city' => ['Achrafieh', 'Jounieh'], 'address' => 'My street', 'postal_code' => '1107',
        ])->assertRedirect(route('business.profile.edit'))->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertSame('Owned services', $profile->refresh()->business_name);
        $this->assertSame('Other business', $otherProfile->refresh()->business_name);
        $this->assertNull($provider->refresh()->email);
        $this->assertSame('owner', $provider->username);
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Owned services')->assertSee('Skilled services')->assertSee('Photography, Transportation')->assertSee('My street')->assertSee('1107');
        $this->get(route('business.profile.edit'))->assertOk()->assertSee('Owned services');
        $this->get('/business/profile/'.$otherProfile->id.'/edit')->assertNotFound();
    }

    public function test_invalid_changes_keep_submitted_fields_without_overwriting_saved_profile(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['business_name' => 'Saved']);
        $this->actingAs($provider)->from(route('business.profile.edit'))->put(route('business.profile.update'), [
            'business_name' => 'Submitted name', 'phone' => 'not a phone', 'state' => ['Beirut'], 'city' => ['Jounieh'],
        ])->assertRedirect(route('business.profile.edit'))->assertSessionHasErrors(['phone', 'city.0'])->assertSessionHasInput('business_name', 'Submitted name');
        $this->assertSame('Saved', $profile->refresh()->business_name);
        $this->get(route('business.profile.edit'))->assertOk()->assertSee('Submitted name')->assertSee('not a phone');
        foreach (['customer', 'admin'] as $state) {
            $this->actingAs(User::factory()->$state()->create())->put(route('business.profile.update'), ['business_name' => 'Unauthorized'])->assertForbidden();
        }
        $this->assertSame('Saved', $profile->refresh()->business_name);
    }
    public function test_orphan_cities_cannot_persist_and_select_all_expands_only_selected_districts(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['state' => ['Beirut', 'Keserwan'], 'city' => ['Achrafieh', 'Jounieh']]);
        $this->actingAs($provider)->put(route('business.profile.update'), ['state' => ['Beirut'], 'city' => ['Achrafieh', 'Jounieh']])
            ->assertSessionHasErrors('city.1');
        $this->assertSame(['Achrafieh', 'Jounieh'], $profile->refresh()->city);
        $this->put(route('business.profile.update'), ['state' => ['Beirut'], 'city' => ['__all__']])->assertSessionHasNoErrors();
        $this->assertSame(\App\Models\BusinessProfile::LEBANON_DISTRICT_CITIES['Beirut'], $profile->refresh()->city);
        $this->put(route('business.profile.update'), ['state' => [], 'city' => ['Achrafieh']])->assertSessionHasErrors('city.0');
        $this->put(route('business.profile.update'), ['state' => [], 'city' => [], 'address' => 'Street', 'postal_code' => '1107'])->assertSessionHasNoErrors();
        $this->assertSame([], $profile->refresh()->state);
        $this->assertSame([], $profile->city);
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Street')->assertSee('1107')->assertDontSee('Cities:');
    }
}
