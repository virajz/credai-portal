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
        Schema::table('exhibitors', function (Blueprint $table) {
            $table->string('gst_number')->nullable()->after('city');
            $table->string('pan_number')->nullable()->after('gst_number');
            $table->text('additional_details')->nullable()->after('facia_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exhibitors', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'pan_number', 'additional_details']);
        });
    }
};
