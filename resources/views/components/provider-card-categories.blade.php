@props(['categories', 'providerName', 'providerId'])

@php
    $hasMore = count($categories) > 3;
    $dialogId = 'provider-categories-'.$providerId;
@endphp

<div @if ($hasMore) x-data="{}" @endif>
    @if ($hasMore)
        <button
            type="button"
            aria-haspopup="dialog"
            aria-controls="{{ $dialogId }}"
            aria-label="View all {{ count($categories) }} work categories for {{ $providerName }}"
            @click="$refs.categoriesDialog.showModal()"
            class="flex w-full cursor-pointer flex-wrap items-center gap-2 rounded-xl text-left outline-offset-4 hover:bg-qareeb-cream focus-visible:outline-2 focus-visible:outline-qareeb-green"
        >
            @foreach (array_slice($categories, 0, 3) as $category)
                <span class="max-w-full rounded-lg border border-qareeb-olive/25 bg-qareeb-olive/10 px-3 py-1.5 text-xs leading-5 font-medium break-words text-qareeb-green">{{ $category }}</span>
            @endforeach
            <span aria-hidden="true" class="flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-qareeb-sand bg-qareeb-cream text-lg font-semibold text-qareeb-green">...</span>
        </button>

        <dialog
            id="{{ $dialogId }}"
            x-ref="categoriesDialog"
            aria-labelledby="{{ $dialogId }}-title"
            aria-describedby="{{ $dialogId }}-provider"
            @click.self="$refs.categoriesDialog.close()"
            class="fixed inset-0 m-auto max-h-[80dvh] w-[calc(100%_-_2rem)] max-w-lg overflow-y-auto rounded-3xl border border-qareeb-sand bg-white p-0 text-qareeb-green shadow-xl backdrop:bg-qareeb-green/40 backdrop:backdrop-blur-sm"
        >
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4 border-b border-qareeb-sand pb-4">
                    <div class="min-w-0">
                        <h3 id="{{ $dialogId }}-title" class="q-heading text-2xl">Work categories</h3>
                        <p id="{{ $dialogId }}-provider" class="q-copy mt-1 text-sm break-words">{{ $providerName }}</p>
                    </div>
                    <form method="dialog" class="shrink-0">
                        <button type="submit" aria-label="Close categories" class="flex size-11 cursor-pointer items-center justify-center rounded-xl hover:bg-qareeb-cream focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-qareeb-green">
                            <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="m6 6 12 12M6 18 18 6" /></svg>
                        </button>
                    </form>
                </div>
                <ul role="list" class="mt-5 flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <li class="max-w-full rounded-xl border border-qareeb-olive/25 bg-qareeb-olive/10 px-3 py-2 text-sm leading-6 font-medium break-words text-qareeb-green">{{ $category }}</li>
                    @endforeach
                </ul>
            </div>
        </dialog>
    @elseif ($categories)
        <ul role="list" class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <li class="max-w-full rounded-lg border border-qareeb-olive/25 bg-qareeb-olive/10 px-3 py-1.5 text-xs leading-5 font-medium break-words text-qareeb-green">{{ $category }}</li>
            @endforeach
        </ul>
    @else
        <p class="q-copy text-sm">No categories listed yet</p>
    @endif
</div>
