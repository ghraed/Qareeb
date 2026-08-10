<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessAccountStatusController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->hasActiveProviderAccount()) {
            return redirect()->route('business.dashboard');
        }

        $content = match ($user->account_status) {
            AccountStatus::Rejected => [
                'eyebrow' => 'Account rejected',
                'title' => 'Your provider account was not approved.',
                'description' => 'An administrator reviewed this business account and marked it as rejected. Review the reason below before contacting the team.',
                'panelClass' => 'border-rose-200 bg-rose-50/80',
                'badgeClass' => 'bg-rose-100 text-rose-700',
                'reasonLabel' => 'Reason provided by the administrator',
                'reason' => $user->rejection_reason,
            ],
            AccountStatus::Suspended => [
                'eyebrow' => 'Account suspended',
                'title' => 'Your provider access is temporarily disabled.',
                'description' => 'This business account was previously active, but an administrator suspended it. Provider dashboard and profile tools stay locked until it is reactivated.',
                'panelClass' => 'border-amber-200 bg-amber-50/80',
                'badgeClass' => 'bg-amber-100 text-amber-800',
                'reasonLabel' => 'Suspension reason',
                'reason' => $user->suspension_reason,
            ],
            default => [
                'eyebrow' => 'Approval in progress',
                'title' => 'Your provider account is waiting for admin approval.',
                'description' => 'The registration was created successfully, but business dashboard and profile features stay locked until an administrator activates the account.',
                'panelClass' => 'border-[#d7ccb6] bg-[#f6f2e6]/85',
                'badgeClass' => 'bg-[#ece4d0] text-[#1f3b2e]',
                'reasonLabel' => null,
                'reason' => null,
            ],
        };

        return view('business.account-status', [
            'user' => $user,
            'content' => $content,
        ]);
    }
}
