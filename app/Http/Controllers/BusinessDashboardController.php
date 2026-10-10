<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user()->load('businessProfile');
        if (! $user->businessProfile) {
            $user->setRelation('businessProfile', $user->businessProfile()->firstOrCreate([], ['business_email' => $user->email, 'is_available_for_hire' => false]));
        }
        $profile = $user->businessProfile;

        return view('business.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'profileCompletion' => $profile->completionPercentage(),
        ]);
    }
}
