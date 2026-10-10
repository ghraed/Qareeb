@props([
    'available' => false,
    'label' => null,
])

@php
    $text = $label ?? ($available ? 'Available for hire' : 'Currently unavailable');
    $classes = $available
        ? 'border-[#d7e3d0] bg-[#f3f8ef] text-[#2e583b]'
        : 'border-[#e6dcc7] bg-[#f7f3ea] text-[#746a57]';
@endphp

<span {{ $attributes->class("inline-flex flex-wrap items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold {$classes}") }}>
    @if ($available)
        <span aria-label="Available for work">#Hiring</span>
    @endif
    <span>{{ $text }}</span>
</span>
