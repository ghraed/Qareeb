<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->string('business_email')->nullable()->after('business_name');
            $table->renameColumn('bio', 'description');
            $table->string('profile_photo')->nullable()->after('description');
            $table->string('brand_photo')->nullable()->after('profile_photo');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->decimal('latitude', 10, 7)->nullable()->after('postal_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('average_rating', 4, 2)->default(0)->after('longitude');
            $table->unsignedInteger('reviews_count')->default(0)->after('average_rating');
            $table->renameColumn('is_available', 'is_available_for_hire');
        });

        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->dropColumn('website_url');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table): void {
            $table->dropUnique(['user_id']);
            $table->string('website_url')->nullable()->after('description');
            $table->renameColumn('description', 'bio');
            $table->renameColumn('is_available_for_hire', 'is_available');
            $table->dropColumn([
                'business_email',
                'profile_photo',
                'brand_photo',
                'city',
                'state',
                'country',
                'postal_code',
                'latitude',
                'longitude',
                'average_rating',
                'reviews_count',
            ]);
        });
    }
};
