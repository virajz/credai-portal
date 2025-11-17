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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('registered_number');
            $table->string('main_person_name');
            $table->string('stall_type')->nullable();
            $table->string('stall_number')->nullable();
            $table->string('stall_size')->nullable();
            $table->decimal('total_payment', 10, 2)->nullable();
            $table->decimal('payment_received', 10, 2)->nullable();
            $table->decimal('payment_pending', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
