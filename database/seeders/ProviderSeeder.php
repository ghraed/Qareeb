<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\BusinessProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Random\Engine\Mt19937;
use Random\Randomizer;
use RuntimeException;

class ProviderSeeder extends Seeder
{
    public const PASSWORD = 'QareebTest2026!';

    private const LOCATIONS = [
        ['Beirut', 'Achrafieh', 33.8886, 35.5190],
        ['Matn', 'Antelias', 33.9167, 35.6000],
        ['Keserwan', 'Jounieh', 33.9808, 35.6178],
        ['Byblos', 'Byblos', 34.1230, 35.6519],
        ['Batroun', 'Batroun', 34.2553, 35.6581],
        ['Tripoli', 'Mina', 34.4500, 35.8167],
        ['Zahlé', 'Zahlé', 33.8463, 35.9020],
        ['Sidon', 'Sidon', 33.5606, 35.3758],
        ['Tyre', 'Tyre', 33.2705, 35.2038],
        ['Chouf', 'Deir El Qamar', 33.6964, 35.5633],
    ];

    private const NAMES = [
        'Maya Khoury', 'Karim Haddad', 'Rana Nassar', 'Jad Saad', 'Nour Salameh',
        'Omar Darwish', 'Lea Mansour', 'Rami Azzi', 'Dalia Hajj', 'Fadi Karam',
    ];

    private const SERVICES = [
        'photography' => [
            'category' => 'Photography', 'label' => 'Photo Studio', 'asset' => 'photography',
            'specialties' => ['wedding and engagement photography', 'family portraits and outdoor sessions', 'landscape and travel photography', 'product photography for local shops', 'small events and birthday photography'],
            'details' => 'Edited digital images are delivered through a private gallery. Packages are agreed before the shoot, with evening and weekend appointments available.',
            'comments' => ['The photos captured the occasion beautifully and the edited gallery arrived on time.', 'Friendly photographer, clear pricing and lovely outdoor portraits.', 'Good results overall; editing took a little longer than expected.', 'The lighting and composition were excellent. We would book another session.'],
        ],
        'gardening' => [
            'category' => 'Gardening', 'label' => 'Garden Care', 'asset' => 'garden',
            'specialties' => ['balcony planting and herb gardens', 'lawn maintenance and seasonal pruning', 'garden design and flower beds', 'drip irrigation and water-saving planting', 'courtyard cleanup and plant care'],
            'details' => 'One-off visits and monthly maintenance plans are available. Plant selection suits the local climate; materials and labour are quoted separately after a site visit.',
            'comments' => ['Our garden looks much healthier after pruning and irrigation adjustments.', 'Arrived on time and left the courtyard tidy. Helpful plant-care advice.', 'The planting was good, although the visit had to be rescheduled.', 'Beautiful flower beds and a practical maintenance plan for our budget.'],
        ],
        'interior-design' => [
            'category' => 'Interior Design', 'label' => 'Interior Studio', 'asset' => 'interior',
            'specialties' => ['small apartment layouts and storage planning', 'living room styling and furniture selection', 'kitchen layouts and lighting plans', 'home office and retail space planning', 'renovation finishes and colour consultations'],
            'details' => 'Consultations include a mood board, a practical layout and a phased budget. Local furniture and finish options are suggested, with project coordination available on request.',
            'comments' => ['The layout made our small apartment feel much more spacious.', 'Thoughtful colour choices and furniture suggestions within our budget.', 'Useful plans, but we needed an extra revision to finalise the lighting.', 'Clear drawings and excellent coordination with our renovation team.'],
        ],
    ];

    public function run(): void
    {
        // Validate bundled assets before making any database changes. No network is needed.
        foreach (self::SERVICES as $service) {
            foreach (range(1, 3) as $number) {
                $source = $this->assetPath($service['asset'], $number);
                if (! is_file($source) || @getimagesize($source) === false) {
                    throw new RuntimeException('Missing or invalid provider seed image: '.$source);
                }
            }
        }

        $random = new Randomizer(new Mt19937(20261011));
        DB::transaction(function () use ($random): void {
            $reviewers = collect(['Hala Habib', 'Tarek Issa', 'Mira Khalil', 'Sami Najjar'])
                ->map(fn (string $name, int $index): User => User::query()->firstOrCreate(
                    ['email' => 'demo.seeker.'.($index + 1).'@example.test'],
                    ['name' => $name, 'password' => self::PASSWORD, 'role' => UserRole::Customer, 'account_status' => AccountStatus::Active],
                ));
            $admin = User::query()->where('email', 'admin@qareeb.com')->where('role', UserRole::Admin)->first();

            foreach (self::SERVICES as $slug => $service) {
                foreach (self::LOCATIONS as $index => [$district, $city, $latitude, $longitude]) {
                    $number = $index + 1;
                    $email = "demo.{$slug}.{$number}@example.test";
                    $user = User::query()->firstOrCreate(['email' => $email], [
                        'name' => self::NAMES[($index + array_search($slug, array_keys(self::SERVICES)) * 3) % 10],
                        'password' => self::PASSWORD,
                        'role' => UserRole::ServiceProvider,
                    ]);
                    if (! $user->isServiceProvider()) {
                        throw new RuntimeException('Seed email belongs to another role: '.$email);
                    }
                    $user->forceFill([
                        'account_status' => AccountStatus::Active, 'email_verified_at' => now()->subDays(90),
                        'approved_at' => now()->subDays(90), 'approved_by' => $admin?->id,
                        'rejected_at' => null, 'rejected_by' => null, 'rejection_reason' => null,
                        'suspended_at' => null, 'suspended_by' => null, 'suspension_reason' => null,
                        'terms_accepted_at' => now()->subDays(91), 'terms_version' => config('terms.version'),
                    ])->save();

                    $images = $random->shuffleArray([1, 2, 3]);
                    $samples = [];
                    foreach (array_slice($images, 0, $random->getInt(2, 3)) as $image) {
                        $samples[] = $this->copyImage($user, $service['asset'], $image, 'work-samples');
                    }
                    $profile = BusinessProfile::query()->updateOrCreate(['user_id' => $user->id], [
                        'business_name' => "{$city} {$service['label']}", 'business_email' => $email,
                        'phone' => '+961 70 '.(120000 + array_search($slug, array_keys(self::SERVICES)) * 100 + $number),
                        'description' => "{$user->name} offers {$service['specialties'][$index % 5]} in {$city}, {$district}, with ".(4 + $index).' years of experience. '.$service['details'],
                        'categories' => [$service['category']],
                        'address' => ['Main Street', 'Market Street', 'Municipality Road', 'Old Town Road', 'Garden Street'][$index % 5].', building '.$number,
                        'city' => [$city], 'state' => [$district], 'country' => 'Lebanon',
                        'latitude' => $latitude, 'longitude' => $longitude,
                        'profile_photo' => $this->copyImage($user, $service['asset'], $images[0], 'profile'),
                        'brand_photo' => $this->copyImage($user, $service['asset'], $images[1], 'brand'),
                        'work_samples' => $samples, 'is_available_for_hire' => $index % 4 !== 3,
                    ]);

                    // Keep actual reviews and summary ratings in sync; preserve reviews from other seekers.
                    $reviewCount = $index % 5;
                    foreach ($reviewers as $reviewIndex => $reviewer) {
                        $query = Review::query()->where('business_profile_id', $profile->id)->where('reviewer_id', $reviewer->id);
                        if ($reviewIndex >= $reviewCount) {
                            $query->delete();

                            continue;
                        }
                        $review = Review::query()->updateOrCreate([
                            'business_profile_id' => $profile->id, 'reviewer_id' => $reviewer->id,
                        ], ['rating' => [5, 4, 3, 5][$reviewIndex], 'comment' => $service['comments'][$reviewIndex]]);
                        $review->forceFill(['created_at' => now()->subDays(7 + $index * 3 + $reviewIndex)])->save();
                    }
                    $profile->update([
                        'reviews_count' => $profile->reviews()->count(),
                        'average_rating' => round((float) $profile->reviews()->avg('rating'), 2),
                    ]);
                }
            }
        });

        $this->command?->info('Seeded 30 providers: 10 each in Photography, Gardening and Interior Design across 10 Lebanese districts.');
    }

    private function assetPath(string $asset, int $number): string
    {
        return __DIR__."/assets/providers/{$asset}-{$number}.jpg";
    }

    private function copyImage(User $user, string $asset, int $number, string $folder): string
    {
        $path = "businesses/{$user->id}/{$folder}/seed-{$asset}-{$number}.jpg";
        if (! Storage::disk('public')->put($path, file_get_contents($this->assetPath($asset, $number)))) {
            throw new RuntimeException('Could not store provider seed image: '.$path);
        }

        return $path;
    }
}
