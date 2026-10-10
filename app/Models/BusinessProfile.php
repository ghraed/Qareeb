<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BusinessProfile extends Model
{
    use HasFactory;

    public const MAX_WORK_SAMPLES = 6;

    public const WORK_CATEGORIES = [
        'Photography',
        'Agriculture and Seasonal Work',
        'Private Tutoring',
        'Technical & Repair Services',
        'Local Produce',
        'Beauty & Personal Care',
        'Electrician',
        'Plumber',
        'Carpenter',
        'Painter',
        'Cleaning',
        'HVAC',
        'Appliance Repair',
        'Construction',
        'Interior Design',
        'Gardening',
        'Transportation',
        'Pest Control',
        'Security Systems',
    ];

    public const LEBANON_DISTRICTS = [
        'Akkar',
        'Aley',
        'Baalbek',
        'Batroun',
        'Becharré',
        'Beirut',
        'Bint Jbeil',
        'Byblos',
        'Chouf',
        'Hasbaya',
        'Hermel',
        'Jezzine',
        'Keserwan',
        'Koura',
        'Marjeyoun',
        'Matn',
        'Miniyeh-Danniyeh',
        'Nabatieh',
        'Rashaya',
        'Sidon',
        'Tripoli',
        'Tyre',
        'West Bekaa',
        'Zahlé',
        'Zgharta',
    ];

    /**
     * @var array<string, list<string>>
     */
    public const LEBANON_DISTRICT_CITIES = [
        'Akkar' => ['Halba', 'Bebnine', 'Qoubaiyat', 'Bireh', 'Miniara', 'Fnideq', 'Mishmish', 'Rahbe'],
        'Aley' => ['Aley', 'Bhamdoun', 'Souk El Gharb', 'Kahhale', 'Ain Dara', 'Baysour', 'Ainab', 'Choueifat'],
        'Baalbek' => ['Baalbek', 'Douris', 'Iaat', 'Brital', 'Yammoune', 'Chmestar', 'Temnine El Faouqa', 'Boudeh'],
        'Batroun' => ['Batroun', 'Chekka', 'Douma', 'Tannourine', 'Kfar Aabida', 'Rachkida', 'Aabdelali', 'Smar Jbeil'],
        'Becharré' => ['Bcharre', 'Hadath El Jebbeh', 'Diman', 'Bqaa Kafra', 'Hasroun', 'Bane', 'Hadchit', 'Tourza'],
        'Beirut' => ['Achrafieh', 'Bachoura', 'Mazraa', 'Moussaitbeh', 'Minet El Hosn', 'Ras Beirut', 'Rmeil', 'Zuqaq El Blat'],
        'Bint Jbeil' => ['Bint Jbeil', 'Ain Ebel', 'Rmeich', 'Tebnine', 'Haris', 'Yaroun', 'Maroun El Ras', 'Aitaroun'],
        'Byblos' => ['Byblos', 'Amchit', 'Blat', 'Halat', 'Aannaya', 'Eddeh', 'Aalma El Jbeil', 'Aqoura'],
        'Chouf' => ['Beiteddine', 'Deir El Qamar', 'Barja', 'Damour', 'Semqaniyeh', 'Moukhtara', 'Jiyeh', 'Mazboud'],
        'Hasbaya' => ['Hasbaya', 'Chebaa', 'Kawkaba', 'Marj El Zohour', 'Rachaya El Foukhar', 'Hebbariyeh', 'Fardis', 'Ain Qinia'],
        'Hermel' => ['Hermel', 'Qasr', 'Zighrine El Hermel', 'Hawsh Al Sayyed Ali', 'Qanafez', 'Mansoura', 'Mrah El Hermel', 'Kouakh'],
        'Jezzine' => ['Jezzine', 'Anane', 'Bkassine', 'Kfarfalous', 'Roum', 'Aaramta', 'Lebaa', 'Kfar Houna'],
        'Keserwan' => ['Jounieh', 'Zouk Mikael', 'Adma', 'Ajaltoun', 'Ballouneh', 'Faqra', 'Faraya', 'Tabarja'],
        'Koura' => ['Amioun', 'Kfar Hazir', 'Deddeh', 'Bziza', 'Ras Masqa', 'Enfeh', 'Kousba', 'Bterram'],
        'Marjeyoun' => ['Marjeyoun', 'Khiam', 'Blida', 'Meiss El Jabal', 'Houla', 'Markaba', 'Qlayaa', 'Deir Mimas'],
        'Matn' => ['Bourj Hammoud', 'Dbayeh', 'Jdeideh', 'Sin El Fil', 'Antelias', 'Baabdat', 'Bikfaya', 'Mansourieh'],
        'Miniyeh-Danniyeh' => ['Miniyeh', 'Sfireh', 'Bqarsouna', 'Deir Ammar', 'Bhannine', 'Kfar Habou', 'Hazmiyeh', 'Izal'],
        'Nabatieh' => ['Nabatieh', 'Kfar Roummane', 'Ansar', 'Yohmor', 'Adshit', 'Arnoun', 'Kfar Tibnit', 'Doueir'],
        'Rashaya' => ['Rashaya', 'Aaqabet Rashaya', 'Ain Aata', 'Kfar Qouq', 'Bakka', 'Mdoukha', 'Bireh Rashaya', 'Tannoura'],
        'Sidon' => ['Sidon', 'Ghaziyeh', 'Maghdoucheh', 'Miyeh w Miyeh', 'Abra', 'Haret Saida', 'Qraiyeh', 'Aadloun'],
        'Tripoli' => ['Tripoli', 'Mina', 'Qalamoun', 'Beddaoui', 'Wadi El Nahleh', 'Abou Samra', 'Bab El Tebbaneh', 'Jabal Mohsen'],
        'Tyre' => ['Tyre', 'Abbasiyeh', 'Borj El Chamali', 'Qana', 'Srifa', 'Ain Baal', 'Bazourieh', 'Naqoura'],
        'West Bekaa' => ['Jeb Jennine', 'Saghbine', 'Machghara', 'Kamed El Loz', 'Sultan Yacoub', 'Lala', 'Ain Zebdeh', 'Baaloul'],
        'Zahlé' => ['Zahlé', 'Chtaura', 'Taalabaya', 'Saadnayel', 'Qabb Elias', 'Riyaq', 'Ksara', 'Makseh'],
        'Zgharta' => ['Zgharta', 'Ehden', 'Miziara', 'Ardeh', 'Kfar Hata', 'Sebaal', 'Aintourine', 'Mejdlaiya'],
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'business_email',
        'phone',
        'description',
        'categories',
        'profile_photo',
        'brand_photo',
        'work_samples',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'average_rating',
        'reviews_count',
        'is_available_for_hire',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'average_rating' => 'decimal:2',
            'reviews_count' => 'integer',
            'is_available_for_hire' => 'boolean',
        ];
    }

    protected function city(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => $this->decodeMultiValueField($value),
            set: fn (mixed $value): ?string => $this->encodeMultiValueField($value),
        );
    }

    protected function state(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => $this->decodeMultiValueField($value),
            set: fn (mixed $value): ?string => $this->encodeMultiValueField($value),
        );
    }

    protected function categories(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => $this->decodeMultiValueField($value),
            set: fn (mixed $value): ?string => $this->encodeMultiValueField($value),
        );
    }

    protected function workSamples(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => $this->decodeMultiValueField($value),
            set: fn (mixed $value): ?string => $this->encodeMultiValueField($value),
        );
    }

    /**
     * @return BelongsTo<User, BusinessProfile>
     */
    public function scopeContainingSelection(Builder $query, string $column, string $value): void
    {
        if (! in_array($column, ['categories', 'state', 'city'], true)) {
            throw new \InvalidArgumentException('Unsupported selection column.');
        }
        // Supports canonical JSON arrays and the legacy single-label storage format.
        $query->where(function ($query) use ($column, $value): void {
            $wrapped = $query->getQuery()->getGrammar()->wrap($column);
            $safeJson = "CASE WHEN JSON_VALID({$wrapped}) THEN {$wrapped} ELSE '[]' END";
            $query->where($column, $value);
            if ($query->getConnection()->getDriverName() === 'sqlite') {
                $query->orWhereRaw("EXISTS (SELECT 1 FROM json_each({$safeJson}) WHERE json_each.value = ?)", [$value]);
            } else {
                $query->orWhereRaw("JSON_CONTAINS({$safeJson}, ?)", [json_encode($value, JSON_UNESCAPED_UNICODE)]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Review, BusinessProfile>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public const COMPLETION_FIELDS = [
        'business_name' => 'Business name', 'phone' => 'Phone', 'description' => 'Description',
        'categories' => 'Work categories', 'address' => 'Address', 'city' => 'Cities',
        'state' => 'Districts', 'profile_photo' => 'Profile photo', 'brand_photo' => 'Brand photo',
    ];

    public static function editableRules(array $districts): array
    {
        return [
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/\A\+?[0-9][0-9 ()-]{5,48}\z/'],
            'description' => ['nullable', 'string', 'max:3000'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::in(BusinessProfile::WORK_CATEGORIES)],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'array'],
            'city.*' => ['string', Rule::in([
                '__all__',
                ...BusinessProfile::citiesForDistricts($districts),
            ])],
            'state' => ['nullable', 'array'],
            'state.*' => ['string', Rule::in(BusinessProfile::LEBANON_DISTRICTS)],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'is_available_for_hire' => ['nullable', 'boolean'],
        ];
    }

    public function missingCompletionFields(): array
    {
        $rules = self::editableRules($this->state);
        $missing = [];
        foreach (self::COMPLETION_FIELDS as $field => $label) {
            if (in_array($field, ['profile_photo', 'brand_photo'], true)) {
                $fieldRules = ['required', 'string', 'regex:#\Abusinesses/'.preg_quote((string) $this->user_id, '#').'/(profile|brand)/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp|heic|heif|avif)\z#'];
            } else {
                $fieldRules = ['required', ...array_filter($rules[$field], fn ($rule) => $rule !== 'nullable')];
                if (in_array($field, ['city', 'state', 'categories'], true)) {
                    $fieldRules[] = 'min:1';
                }
            }
            $fieldRulesMap = [$field => $fieldRules];
            if (isset($rules[$field.'.*'])) {
                $fieldRulesMap[$field.'.*'] = $rules[$field.'.*'];
            }
            // A select-all sentinel is an input control, never persisted completion data.
            if ($field === 'city') {
                $fieldRulesMap['city.*'] = ['string', Rule::in(self::citiesForDistricts($this->state))];
            }
            if (Validator::make([$field => $this->{$field}], $fieldRulesMap)->fails()) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function completionPercentage(): int
    {
        $total = count(self::COMPLETION_FIELDS);

        return (int) round((($total - count($this->missingCompletionFields())) / $total) * 100);
    }

    public function profilePhotoUrl(): ?string
    {
        return $this->storageUrl($this->profile_photo);
    }

    public function brandPhotoUrl(): ?string
    {
        return $this->storageUrl($this->brand_photo);
    }

    /**
     * @return list<string>
     */
    public function workSampleUrls(): array
    {
        return collect($this->work_samples)
            ->map(fn (string $path): ?string => $this->storageUrl($path))
            ->filter(fn (?string $url): bool => filled($url))
            ->values()
            ->all();
    }

    public function locationLine(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->formattedCities(),
            $this->formattedDistricts(),
            $this->postal_code,
        ]);

        return $parts === [] ? null : implode(', ', $parts);
    }

    public function formattedDistricts(): ?string
    {
        $districts = $this->state;

        return $districts === [] ? null : implode(', ', $districts);
    }

    public function formattedCities(): ?string
    {
        $cities = $this->city;

        return $cities === [] ? null : implode(', ', $cities);
    }

    public static function normalizeCategoryFilter(mixed $category): mixed
    {
        return $category === 'Moving' ? 'Transportation' : $category;
    }

    public function formattedCategories(): ?string
    {
        $categories = $this->categories;

        return $categories === [] ? null : implode(', ', $categories);
    }

    /**
     * @return list<string>
     */
    public static function citiesForDistricts(array $districts): array
    {
        return collect($districts)
            ->filter(fn (mixed $district): bool => is_string($district) && array_key_exists($district, self::LEBANON_DISTRICT_CITIES))
            ->flatMap(fn (string $district): array => self::LEBANON_DISTRICT_CITIES[$district])
            ->unique()
            ->values()
            ->all();
    }

    private function storageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return '/storage/'.ltrim($path, '/');
    }

    /**
     * @return list<string>
     */
    private function decodeMultiValueField(?string $value): array
    {
        if (! filled($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return collect($decoded)
                ->filter(fn (mixed $item): bool => is_string($item) && filled($item))
                ->values()
                ->all();
        }

        return [trim($value)];
    }

    private function encodeMultiValueField(mixed $value): ?string
    {
        $values = collect(is_array($value) ? $value : [$value])
            ->filter(fn (mixed $item): bool => is_string($item) && filled(trim($item)))
            ->map(fn (string $item): string => trim($item))
            ->unique()
            ->values()
            ->all();

        return $values === [] ? null : json_encode($values, JSON_UNESCAPED_UNICODE);
    }
}
