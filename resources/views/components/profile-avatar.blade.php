@props([
    'profile',
    'name',
    'size' => 'lg',
    'liveHiring' => false,
])

@php
    $svgId = 'profile-hiring-'.\Illuminate\Support\Str::uuid();
    $sizeMap = match ($size) {
        'sm' => ['wrapper' => 'h-14 w-14', 'avatar' => 'h-14 w-14 text-lg', 'text' => 'text-[7px]', 'ring' => 'h-[150%] w-[150%]'],
        'md' => ['wrapper' => 'h-20 w-20', 'avatar' => 'h-20 w-20 text-2xl', 'text' => 'text-[8px]', 'ring' => 'h-[152%] w-[152%]'],
        'xl' => ['wrapper' => 'h-32 w-32', 'avatar' => 'h-32 w-32 text-4xl', 'text' => 'text-[10px]', 'ring' => 'h-[154%] w-[154%]'],
        default => ['wrapper' => 'h-24 w-24', 'avatar' => 'h-24 w-24 text-3xl', 'text' => 'text-[9px]', 'ring' => 'h-[152%] w-[152%]'],
    };

    $wrapperClass = $sizeMap['wrapper'];
    $avatarClass = $sizeMap['avatar'];
    $textClass = $sizeMap['text'];
    $ringClass = $sizeMap['ring'];
    $showHiringRing = (bool) ($profile?->is_available_for_hire);
@endphp

<div class="relative inline-flex {{ $wrapperClass }} items-center justify-center overflow-visible">
    @if ($profile && $profile->profilePhotoUrl())
        <img src="{{ $profile->profilePhotoUrl() }}" alt="{{ $name }} profile photo" class="{{ $avatarClass }} rounded-full border-4 border-white object-cover shadow-[0_20px_40px_-22px_rgba(31,59,46,0.7)]">
    @else
        <div class="{{ $avatarClass }} flex items-center justify-center rounded-full border-4 border-white bg-[#6c8a5b] font-semibold text-white shadow-[0_20px_40px_-22px_rgba(31,59,46,0.7)]">
            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 2)) }}
        </div>
    @endif

    @if ($showHiringRing || $liveHiring)
        <svg
            @if ($liveHiring)
                x-data="{ hiring: @js($showHiringRing) }"
                @availability-updated.window="hiring = $event.detail.available"
                x-show="hiring"
                @if (! $showHiringRing) style="display: none;" @endif
            @endif
            viewBox="0 0 100 100"
            class="pointer-events-none absolute top-1/2 left-1/2 z-10 {{ $ringClass }} -translate-x-1/2 -translate-y-1/2 overflow-visible"
            aria-hidden="true"
        >
            <defs>
                <linearGradient id="{{ $svgId }}-gradient" x1="10%" y1="25%" x2="90%" y2="85%">
                    <stop offset="0%" stop-color="#6f2cff" stop-opacity="0.25" />
                    <stop offset="18%" stop-color="#7f36ff" stop-opacity="0.98" />
                    <stop offset="75%" stop-color="#a64dff" stop-opacity="0.98" />
                    <stop offset="100%" stop-color="#dfc6ff" stop-opacity="0.5" />
                </linearGradient>
                <filter id="{{ $svgId }}-glow" x="-30%" y="-30%" width="160%" height="160%">
                    <feGaussianBlur stdDeviation="3.2" />
                </filter>
                <path id="{{ $svgId }}-arc-path" d="M 10 50 A 40 40 0 0 0 90 50" />
                <path id="{{ $svgId }}-text-path" d="M 10 50 A 40 40 0 0 0 90 50" />
            </defs>
            <g transform="rotate(28 50 50)">
                <path d="M 10 50 A 40 40 0 0 0 90 50" fill="none" stroke="#ffffff" stroke-opacity="0.18" stroke-width="15" stroke-linecap="round" filter="url(#{{ $svgId }}-glow)" />
                <path d="M 10 50 A 40 40 0 0 0 90 50" fill="none" stroke="url(#{{ $svgId }}-gradient)" stroke-width="15" stroke-linecap="round" />
                <text dominant-baseline="central" text-anchor="middle" class="{{ $textClass }} fill-white font-semibold tracking-[0.12em]">
                    <textPath href="#{{ $svgId }}-text-path" startOffset="50%">#HIRING</textPath>
                </text>
            </g>
        </svg>
    @endif
</div>
