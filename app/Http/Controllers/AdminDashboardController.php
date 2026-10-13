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
        $providers = User::query()->where('role', UserRole::ServiceProvider->value);
        $counts = (clone $providers)->selectRaw('account_status, COUNT(*) as total')
            ->groupBy('account_status')->pluck('total', 'account_status');

        return view('admin.dashboard', [
            'pendingProviders' => (clone $providers)->where('account_status', AccountStatus::Pending->value)
                ->with('businessProfile')->latest()->orderByDesc('id')->paginate(12, ['*'], 'pending_page'),
            'activeProviders' => (clone $providers)->where('account_status', AccountStatus::Active->value)
                ->with('businessProfile')->latest()->orderByDesc('id')->paginate(12, ['*'], 'active_page'),
            'restrictedProviders' => (clone $providers)->whereIn('account_status', [
                AccountStatus::Rejected->value,
                AccountStatus::Suspended->value,
            ])->with('businessProfile')->latest()->orderByDesc('id')->paginate(12, ['*'], 'restricted_page'),
            'stats' => [
                'pending' => $counts[AccountStatus::Pending->value] ?? 0,
                'active' => $counts[AccountStatus::Active->value] ?? 0,
                'rejected' => $counts[AccountStatus::Rejected->value] ?? 0,
                'suspended' => $counts[AccountStatus::Suspended->value] ?? 0,
            ],
        ]);
    }
}
