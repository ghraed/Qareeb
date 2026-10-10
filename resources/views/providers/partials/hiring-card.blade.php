<section class="q-section p-5 sm:p-6">
    <div class="flex items-start gap-4">
        <x-provider-avatar :photo-url="$profile->profilePhotoUrl()" :name="$businessName" size="md" :hiring="$profile->is_available_for_hire" />
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold tracking-[0.22em] text-[#6c8a5b] uppercase">{{ $hiring['status'] }}</p>
            <h2 class="q-heading mt-2 text-2xl leading-tight">{{ $businessName }}</h2>
            <div class="mt-3"><x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" size="sm" /></div>
        </div>
    </div>
    <div class="mt-5"><x-availability-badge :available="$hiring['is_available']" :label="$hiring['status']" /></div>
    <p class="q-copy mt-5 text-sm leading-6">{{ $hiring['message'] }}</p>
    <x-provider-contact :provider="$provider" :profile="$profile" class="mt-6" />
    <p class="q-copy mt-3 text-sm">Qareeb helps you discover providers and contact them directly. Arrangements are made directly with the provider.</p>
</section>
