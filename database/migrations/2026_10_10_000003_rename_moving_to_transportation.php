<?php

use App\Models\BusinessProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $affected = 0;
        DB::transaction(function () use (&$affected): void {
            BusinessProfile::query()->orderBy('id')->chunkById(100, function ($profiles) use (&$affected): void {
                foreach ($profiles as $profile) {
                    if (! in_array('Moving', $profile->categories, true)) {
                        continue;
                    }
                    $categories = array_values(array_unique(array_map(
                        fn (string $category): string => $category === 'Moving' ? 'Transportation' : $category,
                        $profile->categories,
                    )));
                    DB::table('business_profiles')->where('id', $profile->id)->update([
                        'categories' => json_encode($categories, JSON_UNESCAPED_UNICODE),
                    ]);
                    $affected++;
                }
            });
        });
        Log::info('Moving category renamed to Transportation', ['affected_profiles' => $affected]);
    }

    public function down(): void
    {
        // Cannot distinguish migrated assignments from pre-existing Transportation assignments.
    }
};
