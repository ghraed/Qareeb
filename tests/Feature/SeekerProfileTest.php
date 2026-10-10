<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeekerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_profile_is_owned_validated_and_never_publicly_exposed(): void
    {
        $user = User::factory()->customer()->create(['email' => null, 'username' => 'seeker']);
        $other = User::factory()->customer()->create(['phone' => '+96170999999']);
        $this->actingAs($user)->get(route('seeker.profile.edit'))->assertOk();
        $this->put(route('seeker.profile.update'), ['name' => 'Seeker Name', 'phone' => '+96170111222', 'district' => 'Beirut', 'city' => 'Achrafieh', 'user_id' => $other->id, 'role' => 'admin'])
            ->assertRedirect(route('seeker.profile.edit'))->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertSame('+96170111222', $user->refresh()->phone);
        $this->assertSame('+96170999999', $other->refresh()->phone);
        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertArrayNotHasKey('phone', $user->toArray());
        $this->assertArrayNotHasKey('district', $user->toArray());
        $this->get('/seeker/profile/'.$other->id)->assertNotFound();
        $this->get(route('providers.show', $user))->assertNotFound();
        $provider = User::factory()->serviceProvider()->create();
        $provider->businessProfile()->create();
        $this->post(route('providers.reviews.store', $provider), ['rating' => 5, 'comment' => 'Helpful'])->assertSessionHasNoErrors();
        $this->get(route('providers.show', $provider))->assertOk()->assertSee('Seeker Name')->assertDontSee('+96170111222')->assertDontSee('+96170999999');
        foreach (['admin', 'serviceProvider'] as $state) {
            $this->actingAs(User::factory()->$state()->create())->get(route('seeker.profile.edit'))->assertForbidden();
            $this->put(route('seeker.profile.update'), ['name' => 'bad'])->assertForbidden();
        }
    }

    public function test_invalid_location_keeps_input_and_does_not_save(): void
    {
        $user = User::factory()->customer()->create(['name' => 'Saved', 'username' => 'seeker', 'email' => null]);
        $this->actingAs($user)->from(route('seeker.profile.edit'))->put(route('seeker.profile.update'), ['name' => 'Submitted', 'district' => 'Beirut', 'city' => 'Jounieh'])
            ->assertSessionHasErrors('city')->assertSessionHasInput('name', 'Submitted');
        $this->assertSame('Saved', $user->refresh()->name);
        $this->get(route('seeker.profile.edit'))->assertOk()->assertSee('Submitted')->assertSee('Jounieh');
        $this->put(route('seeker.profile.update'), ['name' => 'Valid', 'district' => '', 'city' => 'Achrafieh'])->assertSessionHasErrors('city');
    }

    public function test_optional_identity_changes_require_password_and_enforce_uniqueness_without_requiring_legacy_username(): void
    {
        $user = User::factory()->customer()->create(['email' => 'original@example.com', 'password' => 'password']);
        $this->actingAs($user)->put(route('seeker.profile.update'), ['name' => 'Legacy'])->assertSessionHasNoErrors();
        $this->put(route('seeker.profile.update'), ['name' => 'Legacy', 'email' => 'new@example.com'])->assertSessionHasErrors('current_password');
        $this->put(route('seeker.profile.update'), ['name' => 'Legacy', 'email' => 'new@example.com', 'current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        User::factory()->create(['username' => 'taken']);
        $this->put(route('seeker.profile.update'), ['name' => 'Legacy', 'email' => null, 'username' => 'TAKEN', 'current_password' => 'password'])->assertSessionHasErrors('username');
        $this->put(route('seeker.profile.update'), ['name' => 'Legacy', 'email' => null, 'username' => ' MY.Seeker ', 'current_password' => 'password'])->assertSessionHasNoErrors();
        $this->assertNull($user->refresh()->email);
        $this->assertSame('my.seeker', $user->username);
        $this->assertNull($user->email_verified_at);
        $this->post(route('seeker.logout'));
        $this->post(route('seeker.login.store'), ['email' => 'MY.SEEKER', 'password' => 'password'])->assertRedirect(route('seeker.dashboard'));
    }

    public function test_failed_save_preserves_private_profile_and_never_flashes_password(): void
    {
        $user = User::factory()->customer()->create(['name' => 'Before', 'password' => 'password']);
        User::saving(fn () => false);
        $this->actingAs($user)->from(route('seeker.profile.edit'))->put(route('seeker.profile.update'), ['name' => 'After', 'current_password' => 'password'])
            ->assertSessionHasErrors('name')->assertSessionMissing('_old_input.current_password');
        $this->assertSame('Before', $user->refresh()->name);
    }
}
