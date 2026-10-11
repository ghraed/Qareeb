@php
    $workSampleUrls = $profile->workSampleUrls();
@endphp

<section class="q-section">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="q-heading text-2xl">Work samples</h2>
            <p class="q-copy mt-1 text-sm">Recent visuals from this provider's work and service quality.</p>
        </div>
    </div>

    @if ($workSampleUrls === [])
        <div class="q-panel-soft mt-6 p-6">
            <p class="q-heading text-xl">No gallery images yet</p>
            <p class="q-copy mt-2 text-sm">This provider has not uploaded work sample images yet.</p>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($workSampleUrls as $index => $workSampleUrl)
                <div class="overflow-hidden rounded-[1.75rem] border border-[#eadfc7] bg-white shadow-sm">
                    <img src="{{ $workSampleUrl }}" alt="{{ $profile->business_name ?: $provider->name }} work sample {{ $index + 1 }}" class="h-52 w-full object-cover">
                    <div class="p-3"><x-image-report-action :provider="$provider" type="gallery" :path="$profile->work_samples[$index]" /></div>
                </div>
            @endforeach
        </div>
    @endif
</section>
