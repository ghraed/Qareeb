@extends('layouts.business')

@section('title', 'Business Registration')

@section('content')
    <div class="grid gap-10 lg:grid-cols-[1.08fr_0.92fr] lg:items-center">
        <section class="space-y-7">
            <span class="inline-flex rounded-full border border-[#d7ccb6] bg-[#f6f2e6] px-4 py-2 text-sm font-semibold text-[#1f3b2e]">Business onboarding</span>
            <div class="space-y-4">
                <h1 class="max-w-2xl font-serif text-4xl leading-tight font-semibold tracking-tight text-[#1f3b2e] sm:text-6xl">Open your provider account with the Qareeb brand language customers already trust.</h1>
                <p class="max-w-2xl text-base leading-7 text-[#5f625d] sm:text-lg sm:leading-8">A calm, high-trust onboarding flow for service businesses. Create your account now, then complete your public presence from a private dashboard.</p>
            </div>
            {{-- <div class="grid gap-4 sm:grid-cols-3">
                <div class="q-panel p-5">
                    <p class="text-sm font-medium text-[#6b6b6b]">Color direction</p>
                    <p class="mt-3 flex items-center gap-2">
                        <span class="h-5 w-5 rounded-full bg-[#1f3b2e]"></span>
                        <span class="h-5 w-5 rounded-full bg-[#e8dcc2]"></span>
                        <span class="h-5 w-5 rounded-full bg-[#6c8a5b]"></span>
                    </p>
                </div>
                <div class="q-panel p-5">
                    <p class="text-sm font-medium text-[#6b6b6b]">Typography</p>
                    <p class="mt-2 font-serif text-2xl text-[#1f3b2e]">Fraunces</p>
                    <p class="text-sm text-[#6b6b6b]">Inter body copy</p>
                </div>
                <div class="q-panel p-5">
                    <p class="text-sm font-medium text-[#6b6b6b]">Arabic cue</p>
                    <p class="font-arabic mt-2 text-2xl text-[#1f3b2e]">قريب</p>
                    <p class="text-sm text-[#6b6b6b]">Localized trust</p>
                </div>
            </div> --}}
        </section>

        <section class="q-card p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="font-serif text-3xl font-semibold text-[#1f3b2e]">Business registration</h2>
                <p class="mt-2 text-sm text-[#6b6b6b]">Already have an account? <a href="{{ route('business.login') }}" class="font-semibold text-[#6c8a5b] transition hover:text-[#1f3b2e]">Sign in here</a>.</p>
                <p class="mt-3 rounded-[1.25rem] border border-[#e5dac1] bg-[#fbf8f1] px-4 py-3 text-sm leading-6 text-[#5f625d]">Every new provider account starts in <span class="font-semibold text-[#1f3b2e]">pending</span> status and becomes active only after an administrator approves it.</p>
            </div>

            @if ($errors->any())
                <x-alert type="error" message="Please correct the highlighted fields and try again." class="mb-5" />
            @endif

            <form method="POST" action="{{ route('business.register.store') }}" class="space-y-5" x-data="{ showPassword: false, showConfirmPassword: false }">
                @csrf

                <x-form-input label="Full name" name="name" placeholder="Jordan Lee" autocomplete="name" />
                <x-form-input label="Email" name="email" type="email" placeholder="owner@business.com" autocomplete="email" />

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-[#314236]">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            x-bind:type="showPassword ? 'text' : 'password'"
                            class="q-input border-[#ddd1b8] pr-14 focus:border-[#6c8a5b] focus:ring-[#e8dcc2]"
                            autocomplete="new-password"
                            placeholder="Create a secure password"
                        >
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 right-3 my-auto rounded-full px-3 py-2 text-sm font-medium text-[#6b6b6b] transition hover:bg-[#f6f2e6] hover:text-[#1f3b2e]">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-[#314236]">Confirm password</label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            x-bind:type="showConfirmPassword ? 'text' : 'password'"
                            class="q-input border-[#ddd1b8] pr-14 focus:border-[#6c8a5b] focus:ring-[#e8dcc2]"
                            autocomplete="new-password"
                            placeholder="Repeat your password"
                        >
                        <button type="button" x-on:click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-3 my-auto rounded-full px-3 py-2 text-sm font-medium text-[#6b6b6b] transition hover:bg-[#f6f2e6] hover:text-[#1f3b2e]">
                            <span x-text="showConfirmPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="q-button-primary w-full">
                    Create business account
                </button>
            </form>
        </section>
    </div>
@endsection
