@props([
    'label',
    'name' => 'photo',
    'currentUrl' => null,
    'emptyLabel' => 'No image uploaded yet.',
    'formAction',
    'deleteAction' => null,
])

@php
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

<div class="q-panel p-5" x-data="{ preview: null, fileName: '' }">
    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row">
        <div>
            <h3 class="q-heading text-xl">{{ $label }}</h3>
            <p class="q-copy mt-1 text-sm">{{ $emptyLabel }}</p>
            <p class="mt-1 text-xs font-medium text-[#8f5b28]">Accepted: JPG, PNG, WEBP, HEIC, HEIF, AVIF up to 10 MB.</p>
        </div>
        @if ($deleteAction && $currentUrl)
            <form method="POST" action="{{ $deleteAction }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-[#eadfc7] bg-white px-4 py-2 text-xs font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Remove</button>
            </form>
        @endif
    </div>

    <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-[#eadfc7] bg-white">
        <template x-if="preview">
            <img x-bind:src="preview" alt="Selected image preview" class="h-52 w-full object-cover">
        </template>
        <div x-show="!preview" class="h-52 w-full">
            @if ($currentUrl)
                <img src="{{ $currentUrl }}" alt="{{ $label }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center bg-[#f8f4ea] px-6 text-center text-sm text-[#6b6b6b]">{{ $emptyLabel }}</div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="mt-5 space-y-4">
        @csrf
        <input
            type="file"
            name="{{ $name }}"
            x-ref="fileInput"
            accept="image/jpeg,image/png,image/webp,image/heic,image/heif,image/avif,.jpg,.jpeg,.png,.webp,.heic,.heif,.avif"
            x-on:change="
                preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null;
                fileName = $event.target.files[0] ? $event.target.files[0].name : '';
            "
            class="hidden"
        >
        <div class="flex flex-col gap-3 rounded-[1.15rem] border border-[#ddd1b8] bg-[#fffdf8] p-3 sm:flex-row sm:items-center">
            <button
                type="button"
                x-on:click="$refs.fileInput.click()"
                class="inline-flex w-full items-center justify-center rounded-full bg-[#1f3b2e] px-4 py-3 text-sm font-semibold text-white sm:w-auto"
            >
                Choose file
            </button>
            <p class="min-w-0 break-all text-sm text-[#1f2b23]" x-text="fileName || 'No file chosen'"></p>
        </div>
        @if ($errorBag->has($name))
            <p class="text-sm font-medium text-rose-700">{{ $errorBag->first($name) }}</p>
        @endif
        <button type="submit" class="q-button-secondary w-full sm:w-auto">Upload image</button>
    </form>
</div>
