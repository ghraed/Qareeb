<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use RuntimeException;

/** Explicit, destructive cleanup; deliberately excluded from normal database seeding. */
class ResetProviderAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $removedIds = DB::transaction(function (): array {
            foreach (['rozer@qareeb.com' => UserRole::ServiceProvider, 'admin@qareeb.com' => UserRole::Admin] as $email => $role) {
                if (! User::query()->where('email', $email)->where('role', $role)->exists()) {
                    throw new RuntimeException("Cannot clean accounts: required {$role->value} {$email} is missing.");
                }
            }

            $users = User::query()->where(function ($query): void {
                $query->where(function ($providers): void {
                    $providers->where('role', UserRole::ServiceProvider)->where(function ($emails): void {
                        $emails->where('email', '!=', 'rozer@qareeb.com')->orWhereNull('email');
                    });
                })->orWhere(function ($admins): void {
                    $admins->where('role', UserRole::Admin)->where(function ($emails): void {
                        $emails->where('email', '!=', 'admin@qareeb.com')->orWhereNull('email');
                    });
                });
            })->get();

            foreach ($users as $user) {
                PersonalAccessToken::query()->where('tokenable_type', $user->getMorphClass())->where('tokenable_id', $user->id)->delete();
                if (Schema::hasTable('sessions')) {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                }
                // Foreign keys cascade profiles/reviews and null out moderation references.
                $user->delete();
            }

            return $users->modelKeys();
        });

        foreach ($removedIds as $id) {
            Storage::disk('public')->deleteDirectory('businesses/'.$id);
        }
        $this->command?->info('Removed '.count($removedIds).' accounts; preserved Rozer, the Qareeb admin, and seekers.');
    }
}
