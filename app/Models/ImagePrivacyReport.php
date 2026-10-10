<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagePrivacyReport extends Model
{
    public const REASONS = ['Identifiable person without permission', 'Personal information shown', 'Image ownership concern', 'Other privacy concern'];

    protected $guarded = ['id'];

    protected $hidden = ['image_path', 'description', 'contact_email', 'submission_key', 'dedup_key', 'internal_notes'];

    protected function casts(): array
    {
        return ['status' => ReportStatus::class, 'image_metadata' => 'array', 'reviewed_at' => 'datetime', 'removal_requested_at' => 'datetime'];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
