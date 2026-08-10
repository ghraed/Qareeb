@extends('layouts.business')

@section('title', 'Business Dashboard')

@section('content')
    <div class="space-y-8">
        <section class="q-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold tracking-[0.25em] text-[#6c8a5b] uppercase">Welcome back</p>
                    <h1 class="mt-3 font-serif text-4xl font-semibold tracking-tight text-[#1f3b2e] sm:text-5xl">{{ $user->name }}</h1>
                    <p class="mt-3 text-base text-[#6b6b6b]">{{ $user->email }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('business.profile.edit') }}" class="q-button-secondary">Edit profile</a>
                    <a href="{{ route('providers.show', $user) }}" class="q-button-primary">View public profile</a>
                    <form method="POST" action="{{ route('business.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-[1.15rem] border border-[#eadfc7] bg-[#fff7ef] px-5 py-3 text-sm font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Logout</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="q-card p-6">
                <div class="flex flex-col items-start gap-4 sm:flex-row sm:justify-between">
                    <div>
                        <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Profile completion</h2>
                        <p class="mt-2 text-sm leading-6 text-[#6b6b6b]">Your empty business profile has already been created. Add your business details to strengthen your public presence.</p>
                    </div>
                    <span class="rounded-full bg-[#f3f0e5] px-4 py-2 text-sm font-semibold text-[#1f3b2e]">{{ $profileCompletion }}% complete</span>
                </div>
                <div class="mt-6 h-3 overflow-hidden rounded-full bg-[#ede7d7]">
                    <div class="h-full rounded-full bg-linear-to-r from-[#6c8a5b] to-[#1f3b2e]" style="width: {{ $profileCompletion }}%"></div>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Availability status</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f3b2e]">{{ $profile->is_available ? 'Available' : 'Unavailable' }}</p>
                    </div>
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Business name</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f3b2e]">{{ $profile->business_name ?: 'Not set yet' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6">
                <div class="q-card p-6">
                    <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Next steps</h2>
                    <div class="mt-5 space-y-3">
                        <div class="q-panel p-4">
                            <p class="font-semibold text-[#1f3b2e]">Complete profile details</p>
                            <p class="mt-1 text-sm text-[#6b6b6b]">Add your business name, contact details, and bio.</p>
                        </div>
                        <div class="q-panel p-4">
                            <p class="font-semibold text-[#1f3b2e]">Configure availability</p>
                            <p class="mt-1 text-sm text-[#6b6b6b]">Control whether new customers can see you as active.</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-[2rem] border border-dashed border-[#d5cab2] bg-[#fbf8f1] p-6">
                    <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Upcoming business tools</h2>
                    <p class="mt-2 text-sm leading-6 text-[#6b6b6b]">Placeholder cards for bookings, services, analytics, and customer communication will live here next.</p>
                </div>
            </div>
        </section>
    </div>
@endsection
