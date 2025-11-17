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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibitor_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('area')->nullable();
            $table->string('category')->nullable();
            $table->string('sq_ft')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('handover_date')->nullable(); // Purchase timeline: Within 3 months, Within 6 months, Within a year
            $table->string('status')->nullable(); // ongoing/in development
            $table->string('pdf_path')->nullable();
            $table->string('video_url')->nullable();
            $table->text('usp')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
