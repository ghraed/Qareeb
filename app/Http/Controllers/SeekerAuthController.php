<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Requests\BusinessLoginRequest;
use App\Http\Requests\BusinessRegisterRequest;
use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SeekerAuthController extends Controller
{
    public function registrationForm(): View
    {
        return view('seeker.auth.register');
    }

    public function register(BusinessRegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            ...$request->safe()->only(['name', 'email', 'username', 'password']),
            'role' => UserRole::Customer,
            'account_status' => AccountStatus::Active,
            ...$request->termsConsent(),
        ]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('seeker.dashboard')->with('status', 'Welcome to Qareeb. Your Seeker account is ready.');
    }

    public function loginForm(): View
    {
        return view('seeker.auth.login');
    }

    public function login(BusinessLoginRequest $request): RedirectResponse
    {
        $user = AccountIdentity::authenticate($request->string('email')->toString(), $request->string('password')->toString());
        if (! $user?->isSeeker()) {
            return back()->withInput($request->except('password'))->withErrors(['email' => 'Use your Seeker account credentials to sign in.']);
        }
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route('seeker.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seeker.login')->with('status', 'You have been logged out.');
    }

    public function dashboard(): View
    {
        return view('seeker.dashboard', ['user' => request()->user()]);
    }
}
