<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete exhibitors without a company_id (orphaned records)
        DB::table('exhibitors')->whereNull('company_id')->delete();

        Schema::table('exhibitors', function (Blueprint $table) {
            // Make company_id required (not nullable)
            $table->foreignId('company_id')->nullable(false)->change();

            // Drop redundant fields that exist in companies table
            $table->dropColumn([
                'brand_name',
                'contact_person_name',
                'phone_number',
                'stall_type',
                'stall_number',
                'stall_size',
                'total_payment',
                'payment_received',
                'payment_pending',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exhibitors', function (Blueprint $table) {
            // Make company_id nullable again
            $table->foreignId('company_id')->nullable()->change();

            // Re-add the dropped fields
            $table->string('brand_name')->after('company_id');
            $table->string('contact_person_name')->after('email');
            $table->string('phone_number')->after('contact_person_name');
            $table->string('stall_type')->nullable()->after('additional_details');
            $table->string('stall_number')->nullable()->after('stall_type');
            $table->string('stall_size')->nullable()->after('stall_number');
            $table->decimal('total_payment', 10, 2)->nullable()->after('stall_size');
            $table->decimal('payment_received', 10, 2)->nullable()->after('total_payment');
            $table->decimal('payment_pending', 10, 2)->nullable()->after('payment_received');
        });
    }
};
