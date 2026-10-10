<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AdminLoginRequest;
use App\Support\AccountIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return view('admin.auth.login');
        }

        return $this->redirectAuthenticatedUser();
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $user = AccountIdentity::authenticate($request->string('email')->toString(), $request->string('password')->toString());

        if (! $user) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'The provided credentials are incorrect.',
                ]);
        }

        if (! $user || $user->role !== UserRole::Admin) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'This portal is only available to administrator accounts.',
                ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('status', 'Welcome back to the administrator workspace.');
    }

    public function logout(): RedirectResponse
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        Auth::logout();

        return redirect()
            ->route('admin.login')
            ->with('status', 'You have been logged out of the administrator workspace.');
    }

    private function redirectAuthenticatedUser(): RedirectResponse
    {
        $user = request()->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->isServiceProvider()) {
            return redirect()->route(
                $user->hasActiveProviderAccount() ? 'business.dashboard' : 'business.account.status'
            );
        }

        return redirect()->to('/');
    }
}
