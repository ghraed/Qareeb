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

<span {{ $attributes->class("inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold {$classes}") }}>
    {{ $text }}
</span>
