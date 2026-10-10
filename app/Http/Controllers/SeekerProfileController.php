<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeekerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeekerProfileController extends Controller
{
    public function edit(): View
    {
        return view('seeker.profile', ['user' => request()->user()]);
    }

    public function update(SeekerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $attributes = $request->safe()->only(['name', 'phone', 'district', 'city', 'email', 'username']);
        if ($user->email !== $attributes['email']) {
            $attributes['email_verified_at'] = null;
        }
        try {
            if (! $user->forceFill($attributes)->save()) {
                throw new \RuntimeException('Seeker profile save failed.');
            }
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withInput($request->except(['current_password', 'password', 'password_confirmation']))
                ->withErrors(['name' => 'Your profile could not be saved. Please try again.']);
        }

        return redirect()->route('seeker.profile.edit')->with('status', 'Your private Seeker profile has been saved.');
    }
}
