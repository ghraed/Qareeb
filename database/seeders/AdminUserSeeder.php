<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => (string) config('admin.user.email')],
            [
                'name' => (string) config('admin.user.name'),
                'password' => Hash::make((string) config('admin.user.password')),
                'role' => UserRole::Admin,
                'account_status' => AccountStatus::Active,
                'approved_at' => now(),
                'approved_by' => null,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
                'suspended_at' => null,
                'suspended_by' => null,
                'suspension_reason' => null,
            ],
        );
    }
}
