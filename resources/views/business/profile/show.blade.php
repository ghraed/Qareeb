@extends('layouts.business')

@section('title', 'Business Profile')

@section('content')
    <div class="space-y-6">
        <section class="q-section overflow-hidden p-0">
            <div class="relative h-56 bg-[#f3ecdd] sm:h-72">
                @if ($profile->brandPhotoUrl())
                    <img src="{{ $profile->brandPhotoUrl() }}" alt="{{ $profile->business_name ?: $user->name }} brand photo" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center bg-linear-to-r from-[#1f3b2e] via-[#6c8a5b] to-[#e8dcc2] text-center text-white">
                        <div>
                            <p class="text-sm font-semibold tracking-[0.25em] uppercase text-white/80">Brand cover</p>
                            <h1 class="mt-3 font-serif text-4xl">{{ $profile->business_name ?: $user->name }}</h1>
                        </div>
                    </div>
                @endif

                <div class="absolute left-6 bottom-0 translate-y-1/2 sm:left-8">
                    <x-profile-avatar :profile="$profile" :name="$profile->business_name ?: $user->name" size="xl" />
                </div>
            </div>

            <div class="px-6 pb-6 pt-20 sm:px-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="q-eyebrow">Private business profile</p>
                        <h2 class="q-heading mt-3 text-4xl">{{ $profile->business_name ?: $user->name }}</h2>
                        <p class="q-copy mt-2 text-base">{{ $user->email }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" />
                            <span class="q-badge">{{ $profile->is_available_for_hire ? 'Available for hire' : 'Unavailable' }}</span>
                            <span class="q-badge">{{ $profileCompletion }}% complete</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('business.profile.hiring-availability') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_available_for_hire" value="{{ $profile->is_available_for_hire ? 0 : 1 }}">
                            <button
                                type="submit"
                                class="{{ $profile->is_available_for_hire ? 'inline-flex items-center justify-center rounded-[1.15rem] border border-[#f0c9c9] bg-[#fff5f5] px-5 py-3 text-sm font-semibold text-[#9a3d3d] transition hover:bg-[#ffecec]' : 'q-button-primary' }}"
                            >
                                {{ $profile->is_available_for_hire ? 'Stop hiring' : 'Start hiring' }}
                            </button>
                        </form>
                        <a href="{{ route('business.profile.edit') }}" class="q-button-secondary">Edit profile</a>
                        <a href="{{ route('providers.show', $user) }}" class="q-button-primary">View public profile</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <section class="q-section">
                <h3 class="q-heading text-2xl">Business details</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Phone</p>
                        <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">{{ $profile->phone ?: 'Not added yet' }}</p>
                    </div>
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Reviews</p>
                        <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">{{ $profile->reviews_count }}</p>
                    </div>
                    <div class="q-panel p-5 sm:col-span-2">
                        <p class="text-sm font-medium text-[#6b6b6b]">Work categories</p>
                        <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">{{ $profile->formattedCategories() ?: 'No categories selected yet' }}</p>
                    </div>
                    <div class="q-panel p-5 sm:col-span-2">
                        <p class="text-sm font-medium text-[#6b6b6b]">Description</p>
                        <p class="q-copy mt-2 text-sm leading-6">{{ $profile->description ?: 'Add a description to explain your services, specialties, and value to customers.' }}</p>
                    </div>
                </div>
            </section>

            <x-location-card :profile="$profile" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-image-upload
                label="Profile photo"
                :current-url="$profile->profilePhotoUrl()"
                empty-label="Upload a circular identity photo for your provider card."
                :form-action="route('business.profile.profile-photo.store')"
                :delete-action="route('business.profile.profile-photo.destroy')"
            />
            <x-image-upload
                label="Brand cover photo"
                :current-url="$profile->brandPhotoUrl()"
                empty-label="Upload a wide brand image for your public provider page."
                :form-action="route('business.profile.brand-photo.store')"
                :delete-action="route('business.profile.brand-photo.destroy')"
            />
        </div>

        <x-work-samples-manager
            :profile="$profile"
            :form-action="route('business.profile.work-samples.store')"
            delete-route-name="business.profile.work-samples.destroy"
        />

        <section class="q-section">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="q-heading text-2xl">Latest reviews</h3>
                    <p class="q-copy mt-1 text-sm">Public feedback visible on your marketplace page.</p>
                </div>
                <a href="{{ route('providers.reviews.index', $user) }}" class="q-button-secondary">View all reviews</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($reviews as $review)
                    <x-review-card :review="$review" :provider="$user" />
                @empty
                    <div class="q-panel-soft p-6">
                        <p class="q-heading text-xl">No reviews yet</p>
                        <p class="q-copy mt-2 text-sm">Your public provider page is ready for reviews once customers start leaving feedback.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
