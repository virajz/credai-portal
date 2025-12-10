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
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('age_group')->nullable()->after('phone');
            $table->string('current_residential_area')->nullable()->after('age_group');
            $table->json('weekend_home_types')->nullable()->after('plotting_types');
            $table->dropColumn(['email', 'photo_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->dropColumn(['age_group', 'current_residential_area', 'weekend_home_types']);
        });
    }
};
