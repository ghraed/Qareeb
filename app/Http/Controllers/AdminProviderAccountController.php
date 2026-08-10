<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminProviderAccountController extends Controller
{
    public function approve(Request $request, User $provider): RedirectResponse
    {
        $provider = $this->ensureServiceProvider($provider);

        $provider->update([
            'account_status' => AccountStatus::Active,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);

        $provider->businessProfile()?->update([
            'business_email' => $provider->email,
            'is_available_for_hire' => false,
        ]);

        return back()->with('status', "{$provider->name} has been approved and activated.");
    }

    public function reject(Request $request, User $provider): RedirectResponse
    {
        $provider = $this->ensureServiceProvider($provider);
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $provider->update([
            'account_status' => AccountStatus::Rejected,
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => now(),
            'rejected_by' => $request->user()->id,
            'rejection_reason' => $validated['rejection_reason'],
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('status', "{$provider->name} has been rejected.");
    }

    public function suspend(Request $request, User $provider): RedirectResponse
    {
        $provider = $this->ensureServiceProvider($provider);
        $validated = $request->validate([
            'suspension_reason' => ['required', 'string', 'max:1000'],
        ]);

        $provider->update([
            'account_status' => AccountStatus::Suspended,
            'suspended_at' => now(),
            'suspended_by' => $request->user()->id,
            'suspension_reason' => $validated['suspension_reason'],
        ]);

        return back()->with('status', "{$provider->name} has been suspended.");
    }

    public function activate(Request $request, User $provider): RedirectResponse
    {
        $provider = $this->ensureServiceProvider($provider);

        $provider->update([
            'account_status' => AccountStatus::Active,
            'approved_at' => $provider->approved_at ?? now(),
            'approved_by' => $provider->approved_by ?? $request->user()->id,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);

        $provider->businessProfile()?->update([
            'business_email' => $provider->email,
            'is_available_for_hire' => false,
        ]);

        return back()->with('status', "{$provider->name} has been activated.");
    }

    private function ensureServiceProvider(User $provider): User
    {
        abort_unless($provider->isServiceProvider(), Response::HTTP_NOT_FOUND);

        return $provider;
    }
}
