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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            // Personal Information (Step 1)
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('company_name')->nullable();
            $table->string('photo_path')->nullable();

            // Interests & Preferences (Step 2)
            $table->json('interests'); // ['Residential', 'Commercial', 'Plotting']
            $table->json('residential_types')->nullable(); // ['2 BHK', '3 BHK', etc.]
            $table->json('commercial_types')->nullable(); // ['Showroom', 'Shops', etc.]
            $table->json('plotting_types')->nullable(); // ['Industrial', 'Residential']
            $table->string('planning_to_buy'); // 'Within 3 months', etc.
            $table->json('areas'); // ['Athwa - Vesu', 'Pal - Adajan - Rander', etc.]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
