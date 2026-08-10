<?php

namespace App\Services;

use App\Models\BusinessProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BusinessPhotoService
{
    public function updateProfilePhoto(BusinessProfile $profile, UploadedFile $file): void
    {
        $this->replacePhoto($profile, $file, 'profile_photo', 'profile');
    }

    public function updateBrandPhoto(BusinessProfile $profile, UploadedFile $file): void
    {
        $this->replacePhoto($profile, $file, 'brand_photo', 'brand');
    }

    public function deleteProfilePhoto(BusinessProfile $profile): void
    {
        $this->deletePhoto($profile, 'profile_photo');
    }

    public function deleteBrandPhoto(BusinessProfile $profile): void
    {
        $this->deletePhoto($profile, 'brand_photo');
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function addWorkSamples(BusinessProfile $profile, array $files): void
    {
        $existingSamples = $profile->work_samples;
        $remainingSlots = BusinessProfile::MAX_WORK_SAMPLES - count($existingSamples);

        if ($remainingSlots <= 0) {
            abort(422, 'You can upload up to 6 work sample images.');
        }

        if (count($files) > $remainingSlots) {
            abort(422, "You can upload up to {$remainingSlots} more work sample image(s).");
        }

        $disk = Storage::disk('public');
        $directory = "businesses/{$profile->user_id}/work-samples";
        $newPaths = [];

        foreach ($files as $file) {
            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $newPaths[] = $file->storeAs($directory, $filename, 'public');
        }

        try {
            DB::transaction(function () use ($profile, $existingSamples, $newPaths): void {
                $profile->forceFill([
                    'work_samples' => [...$existingSamples, ...$newPaths],
                ])->save();
            });
        } catch (Throwable $exception) {
            $disk->delete($newPaths);

            throw $exception;
        }
    }

    public function deleteWorkSample(BusinessProfile $profile, int $index): void
    {
        $samples = $profile->work_samples;
        $oldPath = $samples[$index] ?? null;

        if (! $oldPath) {
            abort(404);
        }

        unset($samples[$index]);
        $samples = array_values($samples);

        DB::transaction(function () use ($profile, $samples): void {
            $profile->forceFill(['work_samples' => $samples])->save();
        });

        Storage::disk('public')->delete($oldPath);
    }

    private function replacePhoto(BusinessProfile $profile, UploadedFile $file, string $column, string $folder): void
    {
        $disk = Storage::disk('public');
        $directory = "businesses/{$profile->user_id}/{$folder}";
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $newPath = $file->storeAs($directory, $filename, 'public');
        $oldPath = $profile->{$column};

        try {
            DB::transaction(function () use ($profile, $column, $newPath): void {
                $profile->forceFill([$column => $newPath])->save();
            });
        } catch (Throwable $exception) {
            $disk->delete($newPath);

            throw $exception;
        }

        if ($oldPath) {
            $disk->delete($oldPath);
        }
    }

    private function deletePhoto(BusinessProfile $profile, string $column): void
    {
        $disk = Storage::disk('public');
        $oldPath = $profile->{$column};

        DB::transaction(function () use ($profile, $column): void {
            $profile->forceFill([$column => null])->save();
        });

        if ($oldPath) {
            $disk->delete($oldPath);
        }
    }
}
