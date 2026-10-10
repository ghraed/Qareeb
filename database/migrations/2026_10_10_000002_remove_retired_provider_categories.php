<?php

use App\Models\BusinessProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $removed = ['Home Maintenance', 'Computer Repair', 'Mobile Repair'];
        $affected = 0;
        $categoryLess = [];
        DB::transaction(function () use ($removed, &$affected, &$categoryLess): void {
            BusinessProfile::query()->orderBy('id')->chunkById(100, function ($profiles) use ($removed, &$affected, &$categoryLess): void {
                foreach ($profiles as $profile) {
                    $original = $profile->categories;
                    $remaining = array_values(array_diff($original, $removed));
                    if ($original === $remaining) {
                        continue;
                    }
                    DB::table('business_profiles')->where('id', $profile->id)->update([
                        'categories' => $remaining === [] ? null : json_encode($remaining, JSON_UNESCAPED_UNICODE),
                    ]);
                    $affected++;
                    if ($remaining === []) {
                        $categoryLess[] = $profile->id;
                    }
                }
            });
        });
        Log::info('Retired provider categories removed', ['affected_profiles' => $affected, 'category_less_profile_ids' => $categoryLess]);
    }

    public function down(): void
    {
        // Removed assignments cannot be reconstructed; never invent replacement categories.
    }
};
