@props([
    'rating' => 0,
    'count' => null,
    'size' => 'md',
])

@php
    $starSize = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-6 w-6',
        default => 'h-5 w-5',
    };
@endphp

<div {{ $attributes->class('flex items-center gap-2') }}>
    <div class="flex items-center gap-1">
        @for ($star = 1; $star <= 5; $star++)
            @php
                $fill = max(0, min(100, ($rating - ($star - 1)) * 100));
            @endphp
            <svg viewBox="0 0 20 20" class="{{ $starSize }}" aria-hidden="true">
                <path fill="#e8dcc2" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 0 0 .95-.69l1.07-3.292Z"/>
                <path
                    fill="#c6863a"
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 0 0 .95-.69l1.07-3.292Z"
                    style="clip-path: inset(0 {{ 100 - $fill }}% 0 0);"
                />
            </svg>
        @endfor
    </div>

    <div class="text-sm text-[#6b6b6b]">
        @if (($count ?? 0) > 0)
            <span class="font-semibold text-[#1f3b2e]">{{ number_format((float) $rating, 1) }}</span>
            @if ($count !== null)
                <span>· {{ $count }} {{ \Illuminate\Support\Str::plural('review', $count) }}</span>
            @endif
        @else
            <span class="font-semibold text-[#1f3b2e]">New provider</span>
            <span>· No reviews yet</span>
        @endif
    </div>
</div>
