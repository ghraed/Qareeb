@extends('layouts.business')

@section('title', 'Business Dashboard')

@section('content')
    <div class="space-y-8">
        <section class="q-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold tracking-[0.25em] text-[#6c8a5b] uppercase">Welcome back</p>
                    <h1 class="q-page-title mt-3">{{ $user->name }}</h1>
                    <p class="mt-3 text-base text-[#6b6b6b]">{{ $user->email ?: ($user->username ?: 'Email not provided') }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('business.profile.edit') }}" class="q-button-secondary">Edit profile</a>
                    <a href="{{ route('providers.show', $user) }}" class="q-button-primary">View public profile</a>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="q-card p-6">
                <div class="flex flex-col items-start gap-4 sm:flex-row sm:justify-between">
                    <div>
                        <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Profile completion</h2>
                        <p class="mt-2 text-sm leading-6 text-[#6b6b6b]">Keep your business details complete so Seekers can understand your services.</p>
                    </div>
                    <span class="rounded-full bg-[#f3f0e5] px-4 py-2 text-sm font-semibold text-[#1f3b2e]">{{ $profileCompletion }}% complete</span>
                </div>
                <x-profile-completion :profile="$profile" />
                <div class="mt-6 grid items-start gap-4 sm:grid-cols-2">
                    <div x-data="availabilityToggle(@js((bool) $profile->is_available_for_hire))" @availability-updated.window="available = $event.detail.available" @availability-saving.window="saving = $event.detail.saving" :aria-busy="saving" :class="{ 'border-qareeb-green bg-qareeb-green text-white': available, 'border-qareeb-stone/30 bg-qareeb-stone/5 text-qareeb-stone': !available }" @keydown.escape.window="infoOpen = false" @class([
                        'min-w-0 rounded-2xl border px-4 py-3',
                        'border-qareeb-green bg-qareeb-green text-white' => $profile->is_available_for_hire,
                        'border-qareeb-stone/30 bg-qareeb-stone/5 text-qareeb-stone' => ! $profile->is_available_for_hire,
                    ])>
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1">
                                    <p class="text-sm font-medium">Availability status</p>
                                    <div class="relative" @click.outside="infoOpen = false">
                                        <button type="button" @click="infoOpen = !infoOpen" :aria-expanded="infoOpen" aria-expanded="false" aria-controls="availability-info" aria-label="About availability" class="flex size-11 items-center justify-center rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-current">
                                            <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                                <circle cx="12" cy="12" r="9" />
                                                <path stroke-linecap="round" d="M12 11v6m0-10h.01" />
                                            </svg>
                                        </button>
                                        <div id="availability-info" x-cloak x-show="infoOpen" class="absolute top-full -left-28 z-20 mt-1 w-56 rounded-xl border border-qareeb-sand bg-white p-3 text-sm leading-6 text-qareeb-green shadow-lg">
                                            Availability means accepting enquiries for work.
                                        </div>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold" role="status" x-text="saving ? 'Saving…' : (available ? 'Available' : 'Unavailable')">{{ $profile->is_available_for_hire ? 'Available' : 'Unavailable' }}</p>
                            </div>
                            <form method="POST" action="{{ route('business.profile.hiring-availability') }}" class="shrink-0" @submit.prevent="save($el)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_available_for_hire" :value="available ? 0 : 1" value="{{ $profile->is_available_for_hire ? 0 : 1 }}">
                                <button type="submit" role="switch" :disabled="saving" :aria-checked="available" :title="available ? 'Mark unavailable' : 'Mark availability'" aria-checked="{{ $profile->is_available_for_hire ? 'true' : 'false' }}" aria-label="Accepting enquiries for work" title="{{ $profile->is_available_for_hire ? 'Mark unavailable' : 'Mark availability' }}" class="flex min-h-11 min-w-11 cursor-pointer items-center justify-center rounded-full disabled:cursor-wait disabled:opacity-60 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-current">
                                    <span aria-hidden="true" :class="{ 'justify-end border-white/60 bg-white/25': available, 'justify-start border-qareeb-stone/50 bg-qareeb-stone/30': !available }" @class([
                                        'flex h-7 w-12 items-center rounded-full border p-0.5',
                                        'justify-end border-white/60 bg-white/25' => $profile->is_available_for_hire,
                                        'justify-start border-qareeb-stone/50 bg-qareeb-stone/30' => ! $profile->is_available_for_hire,
                                    ])>
                                        <span class="size-5 rounded-full bg-white shadow-sm"></span>
                                    </span>
                                </button>
                            </form>
                        </div>
                        <p x-cloak x-show="error" x-text="error" class="mt-2 rounded-lg bg-white px-3 py-2 text-sm text-rose-700" role="alert"></p>
                        @error('is_available_for_hire')
                            <p class="mt-2 rounded-lg bg-white px-3 py-2 text-sm text-rose-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Business name</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f3b2e]">{{ $profile->business_name ?: 'Not set yet' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6">
                <x-business-next-steps :profile="$profile" />
                <div class="q-panel p-6 space-y-4">
                    <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Your business details</h2>
                    <p class="q-copy text-sm">{{ $profile->description ?: 'No description added yet.' }}</p>
                    <p class="q-copy text-sm">Categories: {{ $profile->formattedCategories() ?: 'None selected yet' }}</p>
                    <p class="q-copy text-sm">Phone: {{ $profile->phone ?: 'Not listed yet' }}</p>
                    <p class="q-copy text-sm">Location: {{ $profile->locationLine() ?: 'Not listed yet' }}</p>
                </div>
            </div>
        </section>
    </div>
@endsection
