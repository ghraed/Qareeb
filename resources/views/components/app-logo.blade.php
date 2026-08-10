@props(['alt' => 'Qareeb logo'])

<img
    src="{{ asset('images/qareeb-logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->class('object-contain') }}
>
