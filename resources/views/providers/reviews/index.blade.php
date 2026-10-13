@extends('layouts.business')

@section('title', 'Provider Reviews')

@section('content')
    <div class="space-y-6">
        <section class="q-section">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="q-eyebrow">Provider reviews</p>
                    <h1 class="q-page-title mt-3">{{ $profile->business_name ?: $provider->name }}</h1>
                    <div class="mt-4">
                        <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" size="lg" />
                    </div>
                </div>
                <a href="{{ route('providers.show', $provider) }}" class="q-button-secondary">Back to provider</a>
            </div>
        </section>

        @include('providers.reviews.create', ['provider' => $provider])

        <section class="q-section">
            <h2 class="q-heading text-2xl">All reviews</h2>

            <div class="mt-6 space-y-4">
                @forelse ($reviews as $review)
                    <x-review-card :review="$review" :provider="$provider" :editable="auth()->id() === $review->reviewer_id" />
                @empty
                    <div class="q-panel-soft p-6">
                        <p class="q-heading text-xl">No reviews yet</p>
                        <p class="q-copy mt-2 text-sm">This provider does not have any public reviews yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </section>
    </div>
@endsection
