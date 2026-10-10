<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Existing accounts have no inferred historical consent.
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('terms_version')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['terms_accepted_at', 'terms_version']));
    }
};
