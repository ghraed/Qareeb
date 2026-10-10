<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountIdentity
{
    public static function normalize(?string $value): ?string
    {
        $value = strtolower(trim($value ?? ''));

        return $value === '' ? null : $value;
    }

    public static function authenticate(string $identity, string $password): ?User
    {
        $identity = self::normalize($identity);
        $column = str_contains($identity ?? '', '@') ? 'email' : 'username';
        $user = User::query()->whereRaw("LOWER({$column}) = ?", [$identity])->first();

        return $user && Hash::check($password, $user->password) ? $user : null;
    }

    public static function registrationRules(?int $ignoreId = null): array
    {
        // Usernames cannot contain @: username and email namespaces cannot collide.
        return [
            'email' => ['bail', 'nullable', 'string', 'email', 'max:255', self::unique('email', $ignoreId)],
            'username' => ['bail', 'required_without:email', 'nullable', 'string', 'min:3', 'max:50', 'regex:/\A[a-z0-9][a-z0-9_.-]*\z/', self::unique('username', $ignoreId)],
        ];
    }

    private static function unique(string $column, ?int $ignoreId): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($column, $ignoreId): void {
            if (User::query()->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->whereRaw("LOWER({$column}) = ?", [self::normalize($value)])->exists()) {
                $fail("The {$attribute} has already been taken.");
            }
        };
    }
}
