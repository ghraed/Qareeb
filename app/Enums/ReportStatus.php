<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Open = 'open';
    case InReview = 'in_review';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open', self::InReview => 'In Review', self::Resolved => 'Resolved',
        };
    }
}
