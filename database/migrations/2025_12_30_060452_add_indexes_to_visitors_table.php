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
            $table->index('tracking_medium');
            $table->index('created_at');
            $table->index(['name', 'phone', 'company_name', 'current_residential_area'], 'visitors_search_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['tracking_medium']);
            $table->dropIndex(['created_at']);
            $table->dropIndex('visitors_search_index');
        });
    }
};
