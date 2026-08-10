@props([
    'review',
    'editable' => false,
    'provider',
])

<article class="q-panel p-5">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-3">
            <x-profile-avatar :profile="$review->reviewer->businessProfile ?? null" :name="$review->reviewer->name" size="sm" />
            <div>
                <p class="font-semibold text-[#1f3b2e]">{{ $review->reviewer->name }}</p>
                <p class="text-xs text-[#6b6b6b]">{{ $review->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <x-rating-stars :rating="$review->rating" size="sm" />
    </div>

    @if ($review->comment)
        <p class="q-copy mt-4 text-sm leading-6">{{ $review->comment }}</p>
    @endif

    @if ($editable)
        <div class="mt-5 space-y-4 rounded-[1.5rem] border border-[#eadfc7] bg-white p-4">
            <form method="POST" action="{{ route('providers.reviews.update', [$provider, $review]) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="rating-{{ $review->id }}" class="block text-sm font-medium text-[#314236]">Rating</label>
                    <select id="rating-{{ $review->id }}" name="rating" class="q-input mt-2 border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]">
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected(old('rating', $review->rating) == $rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="comment-{{ $review->id }}" class="block text-sm font-medium text-[#314236]">Comment</label>
                    <textarea id="comment-{{ $review->id }}" name="comment" rows="3" class="q-input mt-2 border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]">{{ old('comment', $review->comment) }}</textarea>
                </div>
                <button type="submit" class="q-button-secondary">Update review</button>
            </form>

            <form method="POST" action="{{ route('providers.reviews.destroy', [$provider, $review]) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-[#eadfc7] bg-[#fff7ef] px-4 py-2 text-xs font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Delete review</button>
            </form>
        </div>
    @endif
</article>
