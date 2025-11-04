<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('draft_exhibitors', function (Blueprint $table) {
            $table->id();
            $table->string('resume_token', 64)->unique();
            $table->string('brand_name')->nullable();
            $table->text('office_address')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('video_url')->nullable();
            $table->json('social_media_links')->nullable();
            $table->string('facia_name')->nullable();
            $table->integer('current_step')->default(1);
            $table->json('completed_steps')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('resume_token');
            $table->index('is_completed');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_exhibitors');
    }
};
