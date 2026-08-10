<?php

namespace Tests\Unit;

use App\Models\BusinessProfile;
use Tests\TestCase;

class BusinessProfileUrlTest extends TestCase
{
    public function test_profile_photo_url_uses_relative_storage_path(): void
    {
        $profile = new BusinessProfile([
            'profile_photo' => 'businesses/28/profile/example.jpg',
            'brand_photo' => 'businesses/28/brand/banner.jpg',
            'work_samples' => [
                'businesses/28/work-samples/one.jpg',
                'businesses/28/work-samples/two.jpg',
            ],
        ]);

        $this->assertSame('/storage/businesses/28/profile/example.jpg', $profile->profilePhotoUrl());
        $this->assertSame('/storage/businesses/28/brand/banner.jpg', $profile->brandPhotoUrl());
        $this->assertSame([
            '/storage/businesses/28/work-samples/one.jpg',
            '/storage/businesses/28/work-samples/two.jpg',
        ], $profile->workSampleUrls());
    }
}
