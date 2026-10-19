@props(['type' => 'success', 'message' => ''])

<div
    x-data="toast(@js(['message' => $message, 'type' => $type]))"
    @toast.window="show($event.detail)"
    @if (! $message) x-cloak @endif
    x-show="visible"
    x-transition.opacity.duration.200ms
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    :role="type === 'error' ? 'alert' : 'status'"
    aria-atomic="true"
    @class([
        'fixed top-4 left-1/2 z-[2147483647] flex w-[calc(100%_-_2rem)] max-w-sm -translate-x-1/2 items-center gap-3 rounded-xl border px-4 py-2 shadow-xl motion-reduce:transition-none',
        'border-emerald-200 bg-emerald-50 text-emerald-800' => $type === 'success',
        'border-rose-200 bg-rose-50 text-rose-800' => $type === 'error',
    ])
    :class="{ 'border-emerald-200 bg-emerald-50 text-emerald-800': type === 'success', 'border-rose-200 bg-rose-50 text-rose-800': type === 'error' }"
>
    <svg aria-hidden="true" class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="9" />
        <path x-show="type === 'error'" d="M12 7v6m0 4h.01" />
        <path x-show="type === 'success'" d="m8 12 3 3 5-6" />
    </svg>
    <p x-text="message" class="min-w-0 flex-1 text-sm leading-5 font-medium break-words">{{ $message }}</p>
    <button type="button" @click="dismiss()" aria-label="Dismiss notification" class="flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-lg hover:bg-black/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-current">
        <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M6 18 18 6" /></svg>
    </button>
</div>
