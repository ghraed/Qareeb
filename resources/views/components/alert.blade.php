@props([
    'type' => 'success',
    'message',
])

@php
    $styles = match ($type) {
        'error' => 'border-rose-200 bg-rose-50 text-rose-700',
        default => 'border-[#d7e3d0] bg-[#f3f8ef] text-[#2e583b]',
    };
@endphp

<div {{ $attributes->class("rounded-[1.25rem] border px-4 py-3 text-sm font-medium shadow-sm {$styles}") }}>
    {{ $message }}
</div>
