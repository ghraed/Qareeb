<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user()->loadMissing('businessProfile');
        $profile = $user->businessProfile;

        return view('business.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'profileCompletion' => $profile->completionPercentage(),
        ]);
    }
}
