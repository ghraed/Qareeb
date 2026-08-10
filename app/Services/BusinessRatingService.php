<?php

namespace App\Services;

use App\Models\BusinessProfile;

class BusinessRatingService
{
    public function refresh(BusinessProfile $profile): void
    {
        $aggregates = $profile->reviews()
            ->selectRaw('COALESCE(AVG(rating), 0) as average_rating, COUNT(*) as reviews_count')
            ->first();

        $profile->forceFill([
            'average_rating' => round((float) $aggregates->average_rating, 2),
            'reviews_count' => (int) $aggregates->reviews_count,
        ])->save();
    }
}
