<section class="q-section p-5 sm:p-6" x-data="{ openHiringModal: false }">
    <div class="flex items-start gap-4">
        <x-provider-avatar :photo-url="$profile->profilePhotoUrl()" :name="$businessName" size="md" :hiring="$profile->is_available_for_hire" />
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold tracking-[0.22em] text-[#6c8a5b] uppercase">{{ $hiring['status'] }}</p>
            <h2 class="q-heading mt-2 text-2xl leading-tight">{{ $businessName }}</h2>
            <div class="mt-3">
                <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" size="sm" />
            </div>
        </div>
    </div>

    <div class="mt-5">
        <x-availability-badge :available="$hiring['is_available']" :label="$hiring['status']" />
    </div>

    <p class="q-copy mt-5 text-sm leading-6">{{ $hiring['message'] }}</p>

    @if ($hiring['button_enabled'])
        <button
            type="button"
            x-on:click="openHiringModal = true"
            class="q-button-primary mt-6 w-full"
            aria-label="Hire {{ $businessName }}"
        >
            {{ $hiring['button_label'] }}
        </button>
    @else
        <button
            type="button"
            class="mt-6 inline-flex w-full items-center justify-center rounded-[1.15rem] border border-[#e6dcc7] bg-[#f3eee4] px-5 py-4 text-sm font-semibold text-[#8b816f]"
            disabled
            aria-label="{{ $hiring['button_label'] }}"
        >
            {{ $hiring['button_label'] }}
        </button>
    @endif

    <p class="mt-3 text-xs text-[#6b6b6b]">{{ $hiring['supporting_text'] }}</p>

    <div
        x-cloak
        x-show="openHiringModal"
        x-on:keydown.escape.window="openHiringModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-[#1f2b23]/50 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="hiring-modal-title"
    >
        <div class="q-section max-w-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="q-eyebrow">Hiring coming soon</p>
                    <h3 id="hiring-modal-title" class="q-heading mt-2 text-2xl">{{ $businessName }}</h3>
                </div>
                <button type="button" x-on:click="openHiringModal = false" class="rounded-full border border-[#ddd1b8] bg-white px-3 py-2 text-sm font-semibold text-[#1f3b2e]">
                    Close
                </button>
            </div>
            <p class="q-copy mt-4 text-sm leading-6">Hiring requests for {{ $businessName }} will be available soon. This provider is currently marked as available, and the request workflow will be connected in a future update.</p>
            <p class="mt-4 text-sm font-medium text-[#1f3b2e]">You can review the provider details and recent feedback while this feature is being finalized.</p>
        </div>
    </div>
</section>
