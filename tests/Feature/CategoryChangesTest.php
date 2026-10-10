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
}
