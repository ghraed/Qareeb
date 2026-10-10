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

        $hiring = [
            'is_available' => $profile->is_available_for_hire,
            'status' => $profile->is_available_for_hire ? 'Available for hire' : 'Currently unavailable',
            'message' => $profile->is_available_for_hire
                ? 'This provider is accepting enquiries for work. Contact them directly to discuss your needs.'
                : 'This provider is not accepting new work enquiries right now.',
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
