<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case ServiceProvider = 'service_provider';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Seeker',
            self::ServiceProvider => 'Service Provider',
            self::Admin => 'Admin',
        };
    }
}
