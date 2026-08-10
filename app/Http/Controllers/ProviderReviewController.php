<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Models\User;
use App\Services\BusinessRatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProviderReviewController extends Controller
{
    public function index(User $provider): View
    {
        $provider->load([
            'businessProfile.reviews.reviewer',
        ]);

        return view('providers.reviews.index', [
            'provider' => $provider,
            'profile' => $provider->businessProfile,
            'reviews' => $provider->businessProfile->reviews()->with('reviewer')->latest()->paginate(10),
        ]);
    }

    public function store(StoreReviewRequest $request, User $provider, BusinessRatingService $ratingService): RedirectResponse
    {
        $profile = $provider->businessProfile;

        DB::transaction(function () use ($request, $profile, $ratingService): void {
            $profile->reviews()->create([
                'reviewer_id' => $request->user()->id,
                'rating' => $request->integer('rating'),
                'comment' => $request->string('comment')->toString() ?: null,
            ]);

            $ratingService->refresh($profile);
        });

        return back()->with('status', 'Your review has been submitted.');
    }

    public function update(UpdateReviewRequest $request, User $provider, Review $review, BusinessRatingService $ratingService): RedirectResponse
    {
        Gate::authorize('update', $review);
        abort_unless($review->business_profile_id === $provider->businessProfile->id, 404);

        DB::transaction(function () use ($request, $review, $ratingService): void {
            $review->update([
                'rating' => $request->integer('rating'),
                'comment' => $request->string('comment')->toString() ?: null,
            ]);

            $ratingService->refresh($review->businessProfile);
        });

        return back()->with('status', 'Your review has been updated.');
    }

    public function destroy(User $provider, Review $review, BusinessRatingService $ratingService): RedirectResponse
    {
        Gate::authorize('delete', $review);
        abort_unless($review->business_profile_id === $provider->businessProfile->id, 404);

        DB::transaction(function () use ($review, $ratingService): void {
            $profile = $review->businessProfile;
            $review->delete();
            $ratingService->refresh($profile);
        });

        return back()->with('status', 'Your review has been deleted.');
    }
}
