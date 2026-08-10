<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->boolean('is_available_for_hire')->default(false)->change();
        });

        DB::table('business_profiles')->update([
            'is_available_for_hire' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->boolean('is_available_for_hire')->default(true)->change();
        });
    }
};
