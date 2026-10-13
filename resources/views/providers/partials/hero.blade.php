<section class="q-section overflow-hidden p-0">
    <div class="relative h-40 bg-[#f1e8d4] sm:h-56 lg:h-64">
        @if ($profile->brandPhotoUrl())
            <img src="{{ $profile->brandPhotoUrl() }}" alt="{{ $businessName }} cover photo" class="h-full w-full object-cover">
            <div class="absolute right-3 top-3 z-10"><x-image-report-action :provider="$provider" type="brand" :path="$profile->brand_photo" /></div>
            <div class="absolute inset-0 bg-linear-to-t from-[#1f3b2e]/70 via-[#1f3b2e]/20 to-transparent"></div>
        @else
            <div class="flex h-full items-center justify-center bg-linear-to-r from-[#1f3b2e] via-[#6c8a5b] to-[#e8dcc2]">
                <div class="px-4 text-center text-white sm:px-6">
                    <p class="text-sm font-semibold tracking-[0.25em] uppercase text-white/80">Trusted help nearby</p>
                    <p class="mt-3 font-serif text-2xl font-semibold break-words sm:text-3xl">{{ $businessName }}</p>
                </div>
            </div>
        @endif

        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 sm:left-10 sm:translate-x-0">
            <x-provider-avatar :photo-url="$profile->profilePhotoUrl()" :name="$businessName" size="xl" :hiring="$profile->is_available_for_hire" />
        </div>
    </div>

    <div class="px-4 pb-6 pt-20 sm:px-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="q-eyebrow">Marketplace provider</p>
                <h1 class="q-page-title mt-3">{{ $businessName }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" size="lg" />
                    <x-availability-badge :available="$hiring['is_available']" :label="$hiring['status']" />
                </div>
            </div>

            @if (Route::has('providers.reviews.index'))
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('providers.reviews.index', $provider) }}" class="q-button-secondary">View all reviews</a>
                    <x-image-report-action :provider="$provider" type="profile" :path="$profile->profile_photo" />
                </div>
            @endif
        </div>
    </div>
</section>
