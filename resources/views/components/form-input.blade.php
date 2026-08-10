@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
])

@php
    $wrapperAttributes = $attributes->only('class');
    $inputAttributes = $attributes->except('class');
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

<div {{ $wrapperAttributes->class('space-y-2') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-[#314236]">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $type !== 'password' ? old($name, $value ?? ($slot->isEmpty() ? '' : $slot)) : '' }}"
        placeholder="{{ $placeholder }}"
        @class([
            'q-input',
            'border-rose-300 focus:border-rose-300 focus:ring-rose-100/80' => $errorBag->has($name),
            'border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]' => ! $errorBag->has($name),
        ])
        {{ $inputAttributes }}
    >
    @if ($errorBag->has($name))
        <p class="text-sm font-medium text-rose-700">{{ $errorBag->first($name) }}</p>
    @endif
</div>
