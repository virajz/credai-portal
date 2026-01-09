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
        Schema::table('hourly_winners', function (Blueprint $table) {
            $table->boolean('is_awarded')->default(false)->after('drawn_at');
            $table->timestamp('awarded_at')->nullable()->after('is_awarded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hourly_winners', function (Blueprint $table) {
            $table->dropColumn(['is_awarded', 'awarded_at']);
        });
    }
};
