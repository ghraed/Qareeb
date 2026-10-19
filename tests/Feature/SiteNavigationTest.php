<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_pages_share_public_navigation_without_admin_or_provider_registration_links(): void
    {
        foreach (['/', route('seeker.login'), route('business.login')] as $url) {
            $header = $this->header($this->get($url)->assertOk()->getContent());

            $this->assertStringContainsString(route('seeker.login'), $header);
            $this->assertStringContainsString(route('business.login'), $header);
            if ($url === '/') {
                $this->assertStringContainsString('Seeker login', $header);
                $this->assertStringNotContainsString(route('providers.index'), $header);
                $this->assertStringNotContainsString(route('seeker.register'), $header);
            } else {
                $this->assertStringContainsString(route('providers.index'), $header);
                $this->assertStringContainsString(route('seeker.register'), $header);
            }
            $this->assertStringNotContainsString(route('admin.login'), $header);
            $this->assertStringNotContainsString(route('business.register'), $header);
        }
    }

    public function test_seeker_navigation_uses_private_profile_and_seeker_logout(): void
    {
        $user = User::factory()->customer()->create();
        $header = $this->header($this->actingAs($user)->get(route('seeker.dashboard'))->assertOk()->getContent());

        $this->assertStringContainsString(route('seeker.profile.edit'), $header);
        $this->assertStringContainsString(route('seeker.complaints.index'), $header);
        $this->assertStringContainsString('action="'.route('seeker.logout').'"', $header);
        $this->assertStringContainsString('aria-current="page"', $header);
        $this->assertStringNotContainsString(route('admin.dashboard'), $header);
        $this->assertStringNotContainsString(route('business.dashboard'), $header);
    }

    public function test_provider_navigation_changes_with_approval_status(): void
    {
        $active = User::factory()->serviceProvider()->create();
        $header = $this->header($this->actingAs($active)->get(route('terms.show'))->assertOk()->getContent());

        $this->assertStringContainsString(route('business.dashboard'), $header);
        $this->assertStringContainsString(route('business.profile.show'), $header);
        $this->assertStringContainsString(route('providers.show', $active), $header);
        $this->assertStringContainsString('action="'.route('business.logout').'"', $header);

        $pending = User::factory()->pendingProvider()->create();
        $header = $this->header($this->actingAs($pending)->get(route('terms.show'))->assertOk()->getContent());

        $this->assertStringContainsString(route('business.account.status'), $header);
        $this->assertStringNotContainsString(route('business.dashboard'), $header);
        $this->assertStringNotContainsString('href="'.route('business.profile.show').'"', $header);
        $this->assertStringContainsString('disabled', $header);
    }

    public function test_admin_navigation_uses_review_tools_and_admin_logout(): void
    {
        $user = User::factory()->admin()->create();
        $header = $this->header($this->actingAs($user)->get(route('terms.show'))->assertOk()->getContent());

        $this->assertStringContainsString(route('admin.dashboard'), $header);
        $this->assertStringContainsString(route('admin.complaints.index'), $header);
        $this->assertStringContainsString(route('admin.image-reports.index'), $header);
        $this->assertStringContainsString('action="'.route('admin.logout').'"', $header);
        $this->assertStringNotContainsString(route('seeker.profile.edit'), $header);
    }

    public function test_availability_indicator_is_provider_only_and_matches_saved_status(): void
    {
        $provider = User::factory()->serviceProvider()->create();
        $profile = $provider->businessProfile()->create(['is_available_for_hire' => true]);
        foreach ([true, false] as $available) {
            $profile->update(['is_available_for_hire' => $available]);
            $header = $this->header($this->actingAs($provider->fresh())->get(route('business.dashboard'))->assertOk()->getContent());
            $this->assertStringContainsString('aria-label="Accepting enquiries for work"', $header);
            $this->assertStringContainsString('aria-checked="'.($available ? 'true' : 'false').'"', $header);
            $this->assertStringContainsString('role="switch"', $header);
            $this->assertStringContainsString('action="'.route('business.profile.hiring-availability').'"', $header);
            $this->assertStringContainsString('@availability-updated.window', $header);
        }

        foreach (['customer', 'admin'] as $role) {
            $user = User::factory()->$role()->create();
            $header = $this->header($this->actingAs($user)->get(route('terms.show'))->assertOk()->getContent());
            $this->assertStringNotContainsString('aria-label="Accepting enquiries for work"', $header);
        }
    }

    private function header(string $html): string
    {
        preg_match('/<header\b.*?<\/header>/s', $html, $matches);

        $this->assertNotEmpty($matches);

        return $matches[0];
    }
}
