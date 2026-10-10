<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    public const TYPES = ['Provider conduct', 'Service quality', 'Platform issue', 'Other'];

    protected $fillable = ['user_id', 'provider_id', 'type', 'description', 'submission_token', 'fingerprint', 'status', 'internal_notes', 'reviewed_by', 'reviewed_at'];

    protected $hidden = ['internal_notes', 'fingerprint', 'submission_token', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return ['status' => ReportStatus::class, 'reviewed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
