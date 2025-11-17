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
        Schema::table('draft_exhibitors', function (Blueprint $table) {
            $table->string('stall_type')->nullable()->after('additional_details');
            $table->string('stall_number')->nullable()->after('stall_type');
            $table->string('stall_size')->nullable()->after('stall_number');
            $table->decimal('total_payment', 10, 2)->nullable()->after('stall_size');
            $table->decimal('payment_received', 10, 2)->nullable()->after('total_payment');
            $table->decimal('payment_pending', 10, 2)->nullable()->after('payment_received');
            $table->text('extra_furniture_details')->nullable()->after('payment_pending');
            $table->text('exhibitor_passes_details')->nullable()->after('extra_furniture_details');
            $table->string('momento_name')->nullable()->after('exhibitor_passes_details');
            $table->text('car_pass_details')->nullable()->after('momento_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draft_exhibitors', function (Blueprint $table) {
            $table->dropColumn([
                'stall_type',
                'stall_number',
                'stall_size',
                'total_payment',
                'payment_received',
                'payment_pending',
                'extra_furniture_details',
                'exhibitor_passes_details',
                'momento_name',
                'car_pass_details',
            ]);
        });
    }
};
