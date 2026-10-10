<?php

namespace Tests\Unit;

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class EditBusinessProfileViewTest extends TestCase
{
    public function test_edit_profile_view_renders_saved_values_without_object_string(): void
    {
        $user = new User([
            'name' => 'Provider One',
            'email' => 'provider@example.com',
        ]);

        $profile = new BusinessProfile([
            'business_name' => 'Qareeb Works',
            'business_email' => 'contact@qareeb.test',
            'phone' => '+961 70 000 000',
            'description' => 'Trusted home services.',
            'categories' => ['Electrician', 'Technical & Repair Services'],
            'address' => 'Main street, building 12',
            'city' => 'Beirut',
            'state' => 'Beirut',
            'postal_code' => '1107',
            'average_rating' => 4.5,
            'reviews_count' => 7,
            'is_available_for_hire' => true,
        ]);

        $html = view('business.profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'profileCompletion' => $profile->completionPercentage(),
            'errors' => tap(new ViewErrorBag(), fn (ViewErrorBag $bag) => $bag->put('default', new MessageBag())),
        ])->render();

        $this->assertStringContainsString('value="Qareeb Works"', $html);
        $this->assertStringContainsString('value="provider@example.com"', $html);
        $this->assertStringContainsString('name="business_email_display"', $html);
        $this->assertStringContainsString('disabled', $html);
        $this->assertStringNotContainsString('name="business_email"', $html);
        $this->assertStringContainsString('value="+961 70 000 000"', $html);
        $this->assertStringContainsString('Trusted home services.', $html);
        $this->assertStringContainsString('name="categories[]"', $html);
        $this->assertStringContainsString('Electrician, Technical &amp; Repair Services', $html);
        $this->assertStringContainsString('value="Main street, building 12"', $html);
        $this->assertStringContainsString('name="state[]"', $html);
        $this->assertStringContainsString('name="city[]"', $html);
        $this->assertStringContainsString('\u0022selectedDistricts\u0022:[\u0022Beirut\u0022]', $html);
        $this->assertStringContainsString('\u0022selectedCities\u0022:[\u0022Beirut\u0022]', $html);
        $this->assertStringContainsString('All cities in selected districts', $html);
        $this->assertStringContainsString('value="1107"', $html);
        $this->assertStringNotContainsString('[object HTMLInputElement]', $html);
    }
}
