@props(['provider', 'profile'])
<div {{ $attributes->class('flex flex-wrap gap-3') }}>
    @if ($profile->phone && strlen(preg_replace('/\D+/', '', $profile->phone)) >= 6)
        <a class="q-button-primary" href="tel:{{ preg_replace('/[^0-9+]/', '', $profile->phone) }}">Call provider</a>
    @endif
    @if ($provider->email)
        <a class="q-button-secondary" href="mailto:{{ $provider->email }}">Email provider</a>
    @endif
    @if (! $provider->email && (! $profile->phone || strlen(preg_replace('/\D+/', '', $profile->phone)) < 6))
        <p class="q-copy text-sm">No public contact details have been provided.</p>
    @endif
</div>
