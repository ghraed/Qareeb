@props([
    'profile',
    'public' => false,
])

<div {{ $attributes->class('q-panel p-5') }}>
    <h3 class="q-heading text-xl">Location</h3>
    @if ($profile->locationLine())
        <div class="q-copy mt-3 space-y-2 text-sm leading-6">
            <p>{{ $profile->locationLine() }}</p>
        </div>
    @else
        <p class="q-copy mt-3 text-sm">No location details added yet.</p>
    @endif
</div>
