@extends('layouts.business')
@section('title', 'Edit Seeker profile')
@section('content')
<section class="q-card mx-auto max-w-2xl space-y-6 p-6 sm:p-8">
    <div><p class="q-eyebrow">Private profile</p><h1 class="q-heading mt-3 text-3xl">Your Seeker details</h1><p class="q-copy mt-3">Your phone and location are private. They are never listed in provider discovery or public reviews.</p></div>
    <form method="POST" action="{{ route('seeker.profile.update') }}" class="space-y-5" x-data="{ district: @js(old('district', $user->district)), city: @js(old('city', $user->city)) }">
        @csrf @method('PUT')
        <x-form-input label="Full name" name="name" :value="$user->name" autocomplete="name" required />
        <x-form-input label="Phone" name="phone" :value="$user->phone" autocomplete="tel" />
        <div class="space-y-2">
            <label class="block font-medium" for="district">District</label>
            <select class="q-input" id="district" name="district" x-model="district" x-on:change="city = ''">
                <option value="">Choose a district</option>
                @foreach (\App\Models\BusinessProfile::LEBANON_DISTRICTS as $district)
                    <option value="{{ $district }}" @selected(old('district', $user->district) === $district)>{{ $district }}</option>
                @endforeach
            </select>
            @error('district')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="space-y-2">
            <label class="block font-medium" for="city">City</label>
            <select class="q-input" id="city" name="city" x-model="city">
                <option value="">Choose a city</option>
                @foreach (\App\Models\BusinessProfile::LEBANON_DISTRICT_CITIES as $district => $cities)
                    <optgroup label="{{ $district }}" x-bind:disabled="district !== @js($district)">
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" @selected(old('city', $user->city) === $city)>{{ $city }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('city')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="q-panel space-y-4 p-5">
            <h2 class="q-heading text-xl">Sign-in details</h2>
            <x-form-input label="Email (optional)" name="email" type="email" :value="$user->email" autocomplete="email" />
            <x-form-input label="Username (required without email)" name="username" :value="$user->username" autocomplete="username" />
            <p class="q-copy text-sm">Changing either sign-in detail requires your current password.</p>
            <x-form-input label="Current password (only for sign-in changes)" name="current_password" type="password" autocomplete="current-password" />
        </div>
        <button class="q-button-primary" type="submit">Save private profile</button>
        <a class="q-button-secondary" href="{{ route('seeker.dashboard') }}">Back to dashboard</a>
    </form>
</section>
@endsection
