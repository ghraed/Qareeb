@props(['id', 'records', 'itemsClass' => 'space-y-5'])

<div data-infinite-scroll="{{ $id }}" data-next-url="{{ $records->nextPageUrl() }}" {{ $attributes }}>
    <div data-scroll-items class="{{ $itemsClass }}">
        {{ $slot }}
    </div>
    <div data-scroll-sentinel class="mt-6 flex flex-col items-center gap-3">
        <p data-scroll-status class="q-copy text-sm" role="status" aria-live="polite"></p>
        @if ($records->hasMorePages())
            <a data-scroll-more href="{{ $records->nextPageUrl() }}" class="q-button-secondary">Load more</a>
        @endif
    </div>
</div>
