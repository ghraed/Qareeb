@props([
    'profile',
    'formAction',
    'deleteRouteName',
])

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $samplePaths = $profile->work_samples;
    $sampleUrls = $profile->workSampleUrls();
    $remainingSlots = \App\Models\BusinessProfile::MAX_WORK_SAMPLES - count($samplePaths);
@endphp

<section class="q-panel p-5" x-data="{ selectedCount: 0, selectionError: '', maxSelectable: {{ $remainingSlots }} }">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="q-heading text-xl">Work sample gallery</h3>
            <p class="q-copy mt-1 text-sm">Show up to {{ \App\Models\BusinessProfile::MAX_WORK_SAMPLES }} images of completed work or services.</p>
            <p class="mt-1 text-xs font-medium text-[#8f5b28]">JPG, PNG or WEBP. Up to 5 MB each.</p>
        </div>
        <span class="q-badge text-xs">{{ count($samplePaths) }}/{{ \App\Models\BusinessProfile::MAX_WORK_SAMPLES }} uploaded</span>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($sampleUrls as $index => $sampleUrl)
            <div class="overflow-hidden rounded-[1.5rem] border border-[#eadfc7] bg-white shadow-sm">
                <img src="{{ $sampleUrl }}" alt="Work sample {{ $index + 1 }}" class="h-40 w-full object-cover">
                <div class="flex items-center justify-between gap-3 px-4 py-3">
                    <p class="text-sm font-semibold text-[#1f3b2e]">Sample {{ $index + 1 }}</p>
                    <form method="POST" action="{{ route($deleteRouteName, $index) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="image_ref" value="{{ hash('sha256', $samplePaths[$index]) }}">
                        <button type="submit" class="rounded-full border border-[#eadfc7] bg-white px-3 py-1.5 text-xs font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach

        @for ($slot = 0; $slot < $remainingSlots; $slot++)
            <div class="flex h-[13.25rem] items-center justify-center rounded-[1.5rem] border border-dashed border-[#d5cab2] bg-[#fbf8f1] px-4 text-center text-sm text-[#6b6b6b]">
                Empty sample slot
            </div>
        @endfor
    </div>

    @if ($remainingSlots > 0)
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            <div class="overflow-hidden rounded-[1.5rem] border border-[#eadfc7] bg-white">
                <div class="flex h-52 items-center justify-center bg-[#f8f4ea] px-6 text-center text-sm text-[#6b6b6b]">
                    <span x-show="selectedCount === 0">Upload up to <span x-text="maxSelectable"></span> work sample image<span x-show="maxSelectable !== 1">s</span> in this request.</span>
                    <span x-show="selectedCount > 0"><span x-text="selectedCount"></span> image<span x-show="selectedCount !== 1">s</span> selected for upload.</span>
                </div>
            </div>

            <input
                type="file"
                name="photos[]"
                accept="image/jpeg,image/png,image/webp"
                multiple
                x-on:change="
                    selectionError = '';
                    const count = $event.target.files.length;
                    if (count > maxSelectable) {
                        $event.target.value = '';
                        selectedCount = 0;
                        selectionError = `You can select up to ${maxSelectable} more image(s).`;
                        return;
                    }
                    selectedCount = count;
                "
                class="block w-full rounded-[1.15rem] border border-[#ddd1b8] bg-[#fffdf8] px-4 py-3 text-sm text-[#1f2b23] file:mr-4 file:rounded-full file:border-0 file:bg-[#1f3b2e] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
            >
            <p x-show="selectionError" x-text="selectionError" class="text-sm font-medium text-rose-700"></p>
            @error('photos')
                <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
            @enderror
            @error('photos.*')
                <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
            @enderror
            <button type="submit" class="q-button-secondary">Upload work sample</button>
        </form>
    @else
        <p class="mt-5 text-sm font-medium text-[#8f5b28]">You have reached the {{ \App\Models\BusinessProfile::MAX_WORK_SAMPLES }} image limit. Delete one image to upload another.</p>
    @endif
</section>
