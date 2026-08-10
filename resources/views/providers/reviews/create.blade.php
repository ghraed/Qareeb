@auth
    @if (auth()->id() !== $provider->id)
        <section id="create-review" class="q-section">
            <h2 class="q-heading text-2xl">Leave a review</h2>
            <p class="q-copy mt-2 text-sm">Share a 1 to 5 star rating and optional comment.</p>

            <form method="POST" action="{{ route('providers.reviews.store', $provider) }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="rating" class="block text-sm font-medium text-[#314236]">Rating</label>
                    <select id="rating" name="rating" class="q-input mt-2 border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]">
                        <option value="">Select a rating</option>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                    @error('rating')
                        <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="comment" class="block text-sm font-medium text-[#314236]">Comment</label>
                    <textarea id="comment" name="comment" rows="5" class="q-input mt-2 border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]" placeholder="Describe your experience with this provider.">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="q-button-primary">Submit review</button>
            </form>
        </section>
    @endif
@endauth
