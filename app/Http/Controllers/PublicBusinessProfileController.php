<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\View\View;

class PublicBusinessProfileController extends Controller
{
    public function show(User $provider): View
    {
        abort_unless($provider->role === UserRole::ServiceProvider, 404);

        $provider->load([
            'businessProfile.reviews.reviewer',
        ]);

        return view('providers.show', [
            'provider' => $provider,
            'profile' => $provider->businessProfile,
            'reviews' => $provider->businessProfile->reviews()->with('reviewer')->latest()->take(5)->get(),
        ]);
    }
}
