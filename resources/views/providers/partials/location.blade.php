<section class="q-section">
    <h2 class="q-heading text-2xl">Location</h2>
    <div class="mt-5 q-panel p-5">
        @if ($profile->address || $profile->formattedDistricts() || $profile->formattedCities())
            @if ($profile->address)
                <div>
                    <p class="text-sm font-medium text-[#6b6b6b]">Address</p>
                    <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">{{ $profile->address }}</p>
                </div>
            @endif

            @if ($profile->formattedDistricts() || $profile->formattedCities())
                <div class="{{ $profile->address ? 'mt-5' : '' }}">
                    <p class="text-sm font-medium text-[#6b6b6b]">Service area</p>
                    @if ($profile->formattedDistricts())
                        <p class="mt-2 text-base font-semibold text-[#1f3b2e]">Districts: {{ $profile->formattedDistricts() }}</p>
                    @endif
                    @if ($profile->formattedCities())
                        <p class="mt-2 text-base font-semibold text-[#1f3b2e]">Cities: {{ $profile->formattedCities() }}</p>
                    @endif
                </div>
            @endif
        @else
            <p class="text-sm font-medium text-[#6b6b6b]">Location not provided</p>
        @endif
    </div>
</section>
