<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ToastFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_layout_displays_one_success_toast_on_public_and_auth_pages(): void
    {
        foreach (['providers.index', 'business.login', 'seeker.login', 'admin.login'] as $route) {
            $response = $this->withSession(['status' => 'Saved successfully.'])->get(route($route));

            $response->assertOk()->assertSee('Saved successfully.')
                ->assertSee('role="status"', false)->assertSee('z-[2147483647]', false);
            $this->assertSame(1, substr_count($response->getContent(), 'aria-label="Dismiss notification"'));
        }
    }

    public function test_validation_errors_take_priority_over_success_messages(): void
    {
        $errors = (new ViewErrorBag())->put('default', new MessageBag(['email' => 'Please check your email.']));

        $this->withSession(['errors' => $errors, 'status' => 'Previous success.'])->get(route('seeker.login'))
            ->assertOk()->assertSee('Please check your email.')
            ->assertSee('role="alert"', false)->assertSee('bg-rose-50', false)
            ->assertDontSee('Previous success.');
    }

    public function test_explicit_error_flash_displays_as_an_error_toast(): void
    {
        $this->withSession(['error' => 'The change could not be saved.'])->get(route('seeker.login'))
            ->assertOk()->assertSee('The change could not be saved.')
            ->assertSee('role="alert"', false);
    }
}
