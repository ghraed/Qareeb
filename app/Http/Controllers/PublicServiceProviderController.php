<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class PublicServiceProviderController extends Controller
{
    public function show(User $provider): View
    {
        abort_unless($provider->isServiceProvider() && $provider->hasActiveProviderAccount(), 404);

        $provider->load([
            'businessProfile',
            'businessProfile.reviews.reviewer',
        ]);

        $profile = $provider->businessProfile;

        abort_unless($profile !== null, 404);

        $businessName = $profile->business_name ?: $provider->name;
        $reviews = $profile->reviews()
            ->with('reviewer')
            ->latest()
            ->take(5)
            ->get();

        $location = $profile->locationLine() ?: 'Location not provided';
        $hasReviews = $profile->reviews_count > 0;

        $hiring = $profile->is_available_for_hire
            ? [
                'is_available' => true,
                'status' => 'Available for hire',
                'message' => 'This service provider is currently accepting new hiring requests.',
                'button_label' => 'Hire Now',
                'button_enabled' => true,
                'supporting_text' => 'Response tools and request management will be available soon.',
            ]
            : [
                'is_available' => false,
                'status' => 'Currently unavailable',
                'message' => 'This service provider is not accepting new hiring requests right now.',
                'button_label' => 'Unavailable',
                'button_enabled' => false,
                'supporting_text' => 'Check back later for updated availability.',
            ];

        $seo = [
            'title' => $businessName,
            'description' => $profile->description ?: "{$businessName} public service provider profile.",
            'image' => $profile->brandPhotoUrl() ?: $profile->profilePhotoUrl(),
        ];

        return view('providers.show', [
            'provider' => $provider,
            'profile' => $profile,
            'businessName' => $businessName,
            'reviews' => $reviews,
            'location' => $location,
            'hasReviews' => $hasReviews,
            'hiring' => $hiring,
            'seo' => $seo,
        ]);
    }
}
