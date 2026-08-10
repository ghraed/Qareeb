<?php

namespace Tests\Unit;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class FormInputComponentTest extends TestCase
{
    public function test_alpine_attributes_are_applied_to_the_input_element(): void
    {
        $html = Blade::render(
            '<x-form-input label="Business name" name="business_name" value="Qareeb Works" x-model="businessName" />',
            [
                'errors' => tap(new ViewErrorBag(), fn (ViewErrorBag $bag) => $bag->put('default', new MessageBag())),
            ]
        );

        $this->assertStringContainsString('x-model="businessName"', $html);
        $this->assertStringContainsString('value="Qareeb Works"', $html);
        $this->assertStringNotContainsString('<div x-model="businessName"', $html);
    }
}
