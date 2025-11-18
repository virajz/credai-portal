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
        // Delete draft exhibitors without a company_id (orphaned records)
        DB::table('draft_exhibitors')->whereNull('company_id')->delete();

        Schema::table('draft_exhibitors', function (Blueprint $table) {
            // Make company_id required
            $table->foreignId('company_id')->nullable(false)->change();

            // Remove resume_token as we'll use company_id instead
            $table->dropIndex(['resume_token']);
            $table->dropColumn('resume_token');

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
        Schema::table('draft_exhibitors', function (Blueprint $table) {
            // Make company_id nullable again
            $table->foreignId('company_id')->nullable()->change();

            // Re-add resume_token
            $table->string('resume_token', 64)->unique()->after('id');
            $table->index('resume_token');

            // Re-add dropped fields
            $table->string('brand_name')->nullable()->after('resume_token');
            $table->string('contact_person_name')->nullable()->after('city');
            $table->string('phone_number', 20)->nullable()->after('contact_person_name');
            $table->string('stall_type')->nullable();
            $table->string('stall_number')->nullable();
            $table->string('stall_size')->nullable();
            $table->decimal('total_payment', 10, 2)->nullable();
            $table->decimal('payment_received', 10, 2)->nullable();
            $table->decimal('payment_pending', 10, 2)->nullable();
        });
    }
};
