<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_privacy_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider_name');
            $table->string('image_type', 20);
            $table->string('image_ref', 64);
            $table->string('image_path');
            $table->json('image_metadata');
            $table->string('reason');
            $table->text('description');
            $table->string('contact_email')->nullable();
            $table->string('submission_key', 64)->unique();
            $table->string('dedup_key', 64)->unique();
            $table->string('status')->default('open')->index();
            $table->text('internal_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('removal_reason')->nullable();
            $table->foreignId('removal_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('removal_requested_at')->nullable();
            $table->string('removal_outcome')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_privacy_reports');
    }
};
