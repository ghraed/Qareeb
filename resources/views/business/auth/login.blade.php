@extends('layouts.business')

@section('title', 'Business Login')

@section('content')
    <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <section class="q-card p-5 sm:p-6">
            <div class="mb-6">
                <p class="q-eyebrow mb-3">Provider sign in</p>
                <h1 class="q-page-title">Welcome back to Qareeb.</h1>
                <p class="mt-3 text-base leading-7 text-[#5f625d]">Manage your services, update your profile, and let nearby Seekers know when you are available.</p>
                <p class="mt-3 rounded-[1.25rem] border border-[#e5dac1] bg-[#fbf8f1] px-4 py-3 text-sm leading-6 text-[#5f625d]">Waiting for approval? Sign in to check your account status.</p>
            </div>

            @if ($errors->any())
                <x-alert type="error" message="Login could not be completed. Review the form and try again." class="mb-5" />
            @endif

            <form method="POST" action="{{ route('business.login.store') }}" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                <x-form-input label="Username or email" name="email" type="text" placeholder="owner@business.com" autocomplete="username" />

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-[#314236]">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            x-bind:type="showPassword ? 'text' : 'password'"
                            class="q-input border-[#ddd1b8] pr-14 focus:border-[#6c8a5b] focus:ring-[#e8dcc2]"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        >
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 right-3 my-auto rounded-full px-3 py-2 text-sm font-medium text-[#6b6b6b] transition hover:bg-[#f6f2e6] hover:text-[#1f3b2e]">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <label class="inline-flex items-center gap-3 text-sm text-[#5f625d]">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-[#cdbf9f] text-[#1f3b2e] focus:ring-[#6c8a5b]" @checked(old('remember'))>
                        Remember me
                    </label>

                </div>

                <button type="submit" class="q-button-primary w-full">
                    Sign in
                </button>
            </form>

            <p class="mt-6 text-sm text-[#6b6b6b]">Need a provider account? <a href="{{ route('business.register') }}" class="font-semibold text-[#6c8a5b] transition hover:text-[#1f3b2e]">Register your business</a>.</p>
        </section>

        <section class="hidden space-y-4 lg:block">
            <div class="overflow-hidden rounded-[2rem] bg-[#1f3b2e] p-8 text-white shadow-[0_35px_80px_-30px_rgba(31,59,46,0.95)]">
                <p class="text-xs font-semibold tracking-[0.18em] text-[#d9e4d0] uppercase">Your services, nearby</p>
                <h2 class="mt-4 font-serif text-2xl font-semibold tracking-tight sm:text-3xl">Help people discover what you do best.</h2>
                <p class="mt-4 max-w-xl text-base leading-7 text-[#ecf1e7]">Share your services, add examples of your work, and keep your contact details up to date.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="q-panel p-5">
                    <p class="text-sm font-medium text-[#6b6b6b]">Your profile</p>
                    <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">Services and work samples</p>
                </div>
                <div class="q-panel p-5">
                    <p class="text-sm font-medium text-[#6b6b6b]">Your availability</p>
                    <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">Choose when to accept enquiries</p>
                </div>
            </div>
        </section>
    </div>
@endsection
