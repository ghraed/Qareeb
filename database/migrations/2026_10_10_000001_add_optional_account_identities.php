<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('email')->nullable()->change();
            $table->string('username', 50)->nullable()->unique();
        });
    }

    public function down(): void
    {
        // Email-less accounts cannot safely be converted into required-email accounts.
        throw new RuntimeException('Optional identities require a reviewed data migration before rollback.');
    }
};
