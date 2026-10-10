<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\AccountIdentity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'account_status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'suspended_at',
        'suspended_by',
        'suspension_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'account_status' => AccountStatus::class,
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => AccountIdentity::normalize($value),
        );
    }

    protected function username(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => AccountIdentity::normalize($value),
        );
    }

    /**
     * @return HasOne<BusinessProfile, User>
     */
    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * @return HasMany<Review, User>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * @return BelongsTo<User, User>
     */
    public function approvedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return BelongsTo<User, User>
     */
    public function rejectedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * @return BelongsTo<User, User>
     */
    public function suspendedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSeeker(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isServiceProvider(): bool
    {
        return $this->role === UserRole::ServiceProvider;
    }

    public function hasActiveProviderAccount(): bool
    {
        return $this->isServiceProvider() && $this->account_status === AccountStatus::Active;
    }
}
