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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('firm_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');

            // Interests & Preferences
            $table->json('areas'); // ['Athwa - Vesu', 'Pal - Adajan - Rander', etc.]
            $table->json('property_types'); // ['Apartment', 'Bungalows', etc.]

            // Tracking
            $table->string('tracking_medium')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('phone');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
