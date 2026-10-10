<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeekerCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_profile_edits_update_four_field_completion_without_email_and_views_agree(): void
    {
        $user = User::factory()->customer()->create(['username' => 'seeker', 'email' => null]);
        $this->assertSame(25, $user->seekerCompletionPercentage());
        $this->actingAs($user)->put(route('seeker.profile.update'), ['name' => 'Seeker', 'phone' => '+96170123456', 'district' => 'Beirut', 'city' => 'Achrafieh'])->assertSessionHasNoErrors();
        $this->assertSame(100, $user->refresh()->seekerCompletionPercentage());
        $this->assertSame([], $user->missingSeekerCompletionFields());
        foreach (['seeker.dashboard', 'seeker.profile.edit'] as $route) {
            $this->actingAs($user->fresh())->get(route($route))->assertOk()->assertSee('100% complete')->assertSee('Your profile is complete.');
        }
        $this->put(route('seeker.profile.update'), ['name' => 'Seeker', 'phone' => null, 'district' => 'Beirut', 'city' => 'Achrafieh'])->assertSessionHasNoErrors();
        $this->assertSame(75, $user->refresh()->seekerCompletionPercentage());
        $this->assertSame(['Phone'], $user->missingSeekerCompletionFields());
        $this->actingAs($user->fresh())->get(route('seeker.dashboard'))->assertOk()->assertSee('75% complete')->assertSee('Missing: Phone.');
        $user->update(['city' => 'Jounieh']);
        $this->assertSame(50, $user->refresh()->seekerCompletionPercentage());
        $this->assertSame(['Phone', 'City'], $user->missingSeekerCompletionFields());
    }
}
