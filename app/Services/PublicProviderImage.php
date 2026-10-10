<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\User;

class PublicProviderImage
{
    public static function isOwnedPath(BusinessProfile $profile, string $type, string $path): bool
    {
        $folder = match ($type) {
            'profile' => 'profile', 'brand' => 'brand', 'gallery' => 'work-samples', default => null
        };

        return $folder !== null && (bool) preg_match('#\Abusinesses/'.preg_quote((string) $profile->user_id, '#').'/'.$folder.'/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp|heic|heif|avif|gif)\z#', $path);
    }

    public static function resolve(User $provider, string $type, string $ref): string
    {
        $profile = $provider->businessProfile;
        $paths = match ($type) {
            'profile' => [$profile->profile_photo], 'brand' => [$profile->brand_photo], 'gallery' => $profile->work_samples, default => []
        };
        foreach ($paths as $path) {
            if (is_string($path) && self::isOwnedPath($profile, $type, $path) && hash_equals(hash('sha256', $path), $ref)) {
                return $path;
            }
        }
        abort(404);
    }
}
