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
            $table->text('categories')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('business_profiles')->whereRaw('LENGTH(categories) > 255')->exists()) {
            throw new RuntimeException('Categories exceed 255 characters; rollback would truncate saved selections.');
        }

        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->string('categories')->nullable()->change();
        });
    }
};
