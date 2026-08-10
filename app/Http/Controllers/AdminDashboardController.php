<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $providers = User::query()
            ->where('role', UserRole::ServiceProvider->value)
            ->with('businessProfile')
            ->latest()
            ->get();

        return view('admin.dashboard', [
            'providers' => $providers,
            'pendingProviders' => $providers->where('account_status', AccountStatus::Pending),
            'activeProviders' => $providers->where('account_status', AccountStatus::Active),
            'restrictedProviders' => $providers->whereIn('account_status', [
                AccountStatus::Rejected,
                AccountStatus::Suspended,
            ]),
            'stats' => [
                'pending' => $providers->where('account_status', AccountStatus::Pending)->count(),
                'active' => $providers->where('account_status', AccountStatus::Active)->count(),
                'rejected' => $providers->where('account_status', AccountStatus::Rejected)->count(),
                'suspended' => $providers->where('account_status', AccountStatus::Suspended)->count(),
            ],
        ]);
    }
}
