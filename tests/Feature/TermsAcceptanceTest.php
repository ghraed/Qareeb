<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermsAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_terms_page_links_and_registration_checkboxes_start_unchecked(): void
    {
        $this->get(route('terms.show'))->assertOk()->assertSee(config('terms.version'))->assertSee('does not offer on-site bookings')->assertSee('Email is optional');
        if (! config('terms.approved')) {
            $this->get(route('terms.show'))->assertSee('Draft for owner and legal review')->assertSee('not approved for public launch');
        } else {
            $this->get(route('terms.show'))->assertDontSee('Draft for owner and legal review')->assertDontSee('not approved for public launch')->assertDontSee('Owner/legal review still required');
        }
        foreach (['business', 'seeker'] as $portal) {
            $html = $this->get(route($portal.'.register'))->assertOk()->assertSee(route('terms.show'), false)->getContent();
            preg_match('/<input[^>]*id="terms_accepted"[^>]*>/', $html, $match);
            $this->assertNotEmpty($match);
            $this->assertStringNotContainsString('checked', $match[0]);
        }
        $this->get('/')->assertOk()->assertSee(route('terms.show'), false);
    }

    public function test_both_registrations_require_acceptance_and_current_version_and_record_server_consent(): void
    {
        $this->freezeTime();
        foreach (['business', 'seeker'] as $portal) {
            $data = ['name' => 'Consent '.$portal, 'username' => $portal.'.consent', 'password' => 'password', 'password_confirmation' => 'password', 'terms_version' => config('terms.version')];
            $this->post(route($portal.'.register.store'), $data)->assertSessionHasErrors('terms_accepted');
            $this->assertGuest();
            $this->post(route($portal.'.register.store'), [...$data, 'terms_accepted' => false])->assertSessionHasErrors('terms_accepted');
            $this->post(route($portal.'.register.store'), [...$data, 'terms_accepted' => true, 'terms_version' => 'obsolete-version'])->assertSessionHasErrors('terms_version');
            $this->assertSame(0, User::count());
            $this->post(route($portal.'.register.store'), [...$data, 'terms_accepted' => true, 'terms_accepted_at' => '1990-01-01'])->assertSessionHasNoErrors();
            $user = User::where('username', $portal.'.consent')->firstOrFail();
            $this->assertSame(config('terms.version'), $user->terms_version);
            $this->assertSame(now()->toDateTimeString(), $user->terms_accepted_at->toDateTimeString());
            $this->post(route($portal.'.logout'));
            $user->delete();
        }
    }

    public function test_existing_accounts_do_not_get_fabricated_consent_or_forced_reacceptance_on_login_or_edit(): void
    {
        $user = User::factory()->customer()->create(['password' => 'password']);
        $this->assertNull($user->terms_accepted_at);
        $this->assertNull($user->terms_version);
        $this->post(route('seeker.login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasNoErrors();
        $this->put(route('seeker.profile.update'), ['name' => 'Legacy Seeker'])->assertSessionHasNoErrors();
        $this->assertNull($user->refresh()->terms_accepted_at);
        $this->assertNull($user->terms_version);
    }
}
