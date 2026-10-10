@props([
    'photoUrl' => null,
    'name',
    'size' => 'lg',
    'hiring' => false,
])

@php
    $sizeMap = match ($size) {
        'sm' => ['wrapper' => 'h-12 w-12', 'avatar' => 'h-12 w-12 text-base', 'text' => 'text-[7px]', 'ring' => 'h-[150%] w-[150%]'],
        'md' => ['wrapper' => 'h-16 w-16', 'avatar' => 'h-16 w-16 text-xl', 'text' => 'text-[8px]', 'ring' => 'h-[152%] w-[152%]'],
        'xl' => ['wrapper' => 'h-32 w-32', 'avatar' => 'h-32 w-32 text-4xl', 'text' => 'text-[10px]', 'ring' => 'h-[154%] w-[154%]'],
        default => ['wrapper' => 'h-24 w-24', 'avatar' => 'h-24 w-24 text-3xl', 'text' => 'text-[9px]', 'ring' => 'h-[152%] w-[152%]'],
    };

    $wrapperClass = $sizeMap['wrapper'];
    $avatarClass = $sizeMap['avatar'];
@endphp

<div class="relative inline-flex {{ $wrapperClass }} items-center justify-center overflow-visible rounded-full {{ $hiring ? 'ring-4 ring-[#6c8a5b] ring-offset-2' : '' }}">
    @if ($photoUrl)
        <img src="{{ $photoUrl }}" alt="{{ $name }} profile photo" class="{{ $avatarClass }} rounded-full border-4 border-white object-cover shadow-[0_20px_40px_-22px_rgba(31,59,46,0.7)]">
    @else
        <div class="{{ $avatarClass }} flex items-center justify-center rounded-full border-4 border-white bg-[#6c8a5b] font-semibold text-white shadow-[0_20px_40px_-22px_rgba(31,59,46,0.7)]">
            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 1)) }}
        </div>
    @endif


</div>
