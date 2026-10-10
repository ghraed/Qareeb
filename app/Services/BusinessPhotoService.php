<?php

namespace App\Services;

use App\Models\BusinessProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
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

    public function addWorkSamples(BusinessProfile $profile, array $files): void
    {
        $newPaths = [];
        try {
            DB::transaction(function () use ($profile, $files, &$newPaths): void {
                $current = BusinessProfile::query()->lockForUpdate()->findOrFail($profile->id);
                if (count($current->work_samples) + count($files) > BusinessProfile::MAX_WORK_SAMPLES) {
                    throw ValidationException::withMessages(['photos' => 'You can upload up to 6 work sample images.']);
                }
                foreach ($files as $file) {
                    $newPaths[] = $this->store($current, $file, 'work-samples');
                }
                if (! $current->forceFill(['work_samples' => [...$current->work_samples, ...$newPaths]])->save()) {
                    throw new RuntimeException('Image persistence failed.');
                }
            });
        } catch (Throwable $exception) {
            foreach ($newPaths as $path) {
                $this->cleanup($profile, $path);
            }
            throw $exception;
        }
        $profile->refresh();
    }

    public function deleteWorkSample(BusinessProfile $profile, int $index, ?string $imageRef = null): void
    {
        $oldPath = DB::transaction(function () use ($profile, $index, $imageRef): string {
            $current = BusinessProfile::query()->lockForUpdate()->findOrFail($profile->id);
            $samples = $current->work_samples;
            $oldPath = $samples[$index] ?? null;
            abort_unless($oldPath, 404);
            if ($imageRef !== null && ! hash_equals(hash('sha256', $oldPath), $imageRef)) {
                throw ValidationException::withMessages(['photos' => 'The gallery changed. Refresh and select the image again.']);
            }
            unset($samples[$index]);
            if (! $current->forceFill(['work_samples' => array_values($samples)])->save()) {
                throw new RuntimeException('Image persistence failed.');
            }

            return $oldPath;
        });
        $this->cleanup($profile, $oldPath);
        $profile->refresh();
    }

    private function replacePhoto(BusinessProfile $profile, UploadedFile $file, string $column, string $folder): void
    {
        $newPath = $this->store($profile, $file, $folder);
        try {
            $oldPath = DB::transaction(function () use ($profile, $column, $newPath): ?string {
                $current = BusinessProfile::query()->lockForUpdate()->findOrFail($profile->id);
                $oldPath = $current->{$column};
                if (! $current->forceFill([$column => $newPath])->save()) {
                    throw new RuntimeException('Image persistence failed.');
                }

                return $oldPath;
            });
        } catch (Throwable $exception) {
            $this->cleanup($profile, $newPath);
            throw $exception;
        }
        $this->cleanup($profile, $oldPath);
        $profile->refresh();
    }

    private function deletePhoto(BusinessProfile $profile, string $column): void
    {
        $oldPath = DB::transaction(function () use ($profile, $column): ?string {
            $current = BusinessProfile::query()->lockForUpdate()->findOrFail($profile->id);
            $oldPath = $current->{$column};
            if (! $current->forceFill([$column => null])->save()) {
                throw new RuntimeException('Image persistence failed.');
            }

            return $oldPath;
        });
        $this->cleanup($profile, $oldPath);
        $profile->refresh();
    }

    public function removeReportedImage(BusinessProfile $profile, string $type, string $path): bool
    {
        if (! PublicProviderImage::isOwnedPath($profile, $type, $path)) {
            throw ValidationException::withMessages(['removal_reason' => 'The image is not an owning managed image.']);
        }
        $removedCurrent = DB::transaction(function () use ($profile, $type, $path): bool {
            $current = BusinessProfile::query()->lockForUpdate()->findOrFail($profile->id);
            if (BusinessProfile::query()->whereKeyNot($current->id)->where(fn ($query) => $query
                ->where('profile_photo', $path)->orWhere('brand_photo', $path)->orWhere('work_samples', $path)
                ->orWhere('work_samples', 'like', '%'.json_encode($path).'%'))->exists()) {
                throw ValidationException::withMessages(['removal_reason' => 'This file is referenced by another profile.']);
            }
            if ($type === 'gallery') {
                if (! in_array($path, $current->work_samples, true)) {
                    return false;
                }
                $current->work_samples = array_values(array_diff($current->work_samples, [$path]));
            } else {
                $column = $type === 'profile' ? 'profile_photo' : 'brand_photo';
                if ($current->{$column} !== $path) {
                    return false;
                }
                $current->{$column} = null;
            }
            if (! $current->save()) {
                throw new RuntimeException('Reported image removal could not be saved.');
            }

            return true;
        });
        $this->cleanup($profile, $path);
        $profile->refresh();

        return $removedCurrent;
    }

    private function store(BusinessProfile $profile, UploadedFile $file, string $folder): string
    {
        $path = $file->storeAs("businesses/{$profile->user_id}/{$folder}", Str::uuid().'.'.$file->extension(), 'public');
        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Image storage failed.');
        }

        return $path;
    }

    private function cleanup(BusinessProfile $profile, ?string $path): void
    {
        if (! $path || ! preg_match('#\Abusinesses/'.preg_quote((string) $profile->user_id, '#').'/(profile|brand|work-samples)/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp|heic|heif|avif|gif)\z#', $path)) {
            return;
        }
        // Keep files still referenced by any profile; never trust arbitrary stored paths.
        $referenced = BusinessProfile::query()->where('profile_photo', $path)->orWhere('brand_photo', $path)
            ->orWhere('work_samples', $path)->orWhere('work_samples', 'like', '%'.json_encode($path).'%')->exists();
        if (! $referenced) {
            if (! Storage::disk('public')->delete($path)) {
                throw new RuntimeException('Managed image cleanup failed.');
            }
        }
    }
}
