<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Requests\BusinessLoginRequest;
use App\Http\Requests\BusinessRegisterRequest;
use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessAuthController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('business.auth.register');
    }

    public function register(BusinessRegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::create([
                'name' => $request->string('name')->toString(),
                'email' => $request->input('email'),
                'username' => $request->input('username'),
                'password' => $request->string('password')->toString(),
                'role' => UserRole::ServiceProvider,
                'account_status' => AccountStatus::Pending,
            ]);

            $user->businessProfile()->create([
                'business_email' => $user->email,
                'is_available_for_hire' => false,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('business.account.status')
            ->with('status', 'Your business account has been created and is now waiting for admin approval.');
    }

    public function showLoginForm(): View
    {
        return view('business.auth.login');
    }

    public function login(BusinessLoginRequest $request): RedirectResponse
    {
        $user = AccountIdentity::authenticate($request->string('email')->toString(), $request->string('password')->toString());

        if (! $user) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'The provided credentials are incorrect.',
                ]);
        }

        if ($user->isAdmin()) {
            return app(AdminAuthController::class)->signIn($user, $request);
        }

        if (! $user || $user->role !== UserRole::ServiceProvider) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'This portal is only available to service provider accounts.',
                ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $request->session()->forget('url.intended');

        return redirect()
            ->route($user->hasActiveProviderAccount() ? 'business.dashboard' : 'business.account.status')
            ->with('status', $user->hasActiveProviderAccount()
                ? 'Welcome back to your business account.'
                : 'Your provider account is signed in, but access stays limited until an administrator activates it.');
    }

    public function logout(): RedirectResponse
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        Auth::logout();

        return redirect()
            ->route('business.login')
            ->with('status', 'You have been logged out.');
    }
}
