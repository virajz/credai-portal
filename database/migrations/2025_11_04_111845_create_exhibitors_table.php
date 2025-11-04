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
        Schema::create('exhibitors', function (Blueprint $table) {
            $table->id();

            // Company Details
            $table->string('brand_name');
            $table->text('office_address');
            $table->string('city');

            // Contact Person
            $table->string('contact_person_name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Branding & Media
            $table->string('logo_path')->nullable();
            $table->string('brochure_path')->nullable();
            $table->json('photos')->nullable(); // Array of {path, label}
            $table->string('video_url')->nullable();
            $table->json('social_media_links')->nullable(); // {facebook, linkedin, instagram}

            // Exhibition Display
            $table->string('facia_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibitors');
    }
};
