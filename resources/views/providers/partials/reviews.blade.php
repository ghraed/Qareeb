<section class="q-section">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="q-heading text-2xl">Recent reviews</h2>
            <p class="q-copy mt-1 text-sm">Recent public feedback from customers and community members.</p>
        </div>
        @if (Route::has('providers.reviews.index'))
            <a href="{{ route('providers.reviews.index', $provider) }}" class="q-button-secondary">View all reviews</a>
        @endif
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($reviews as $review)
            <x-review-card :review="$review" :provider="$provider" :editable="auth()->id() === $review->reviewer_id" />
        @empty
            <div class="q-panel-soft p-6">
                <p class="q-heading text-xl">No reviews yet</p>
                <p class="q-copy mt-2 text-sm">This provider has not received any reviews.</p>
            </div>
        @endforelse
    </div>
</section>
