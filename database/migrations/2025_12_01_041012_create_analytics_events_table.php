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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // e.g., 'profile_view', 'project_view', 'download_brochure', 'call_click'
            $table->string('trackable_type')->nullable(); // e.g., 'App\Models\Exhibitor', 'App\Models\Project'
            $table->unsignedBigInteger('trackable_id')->nullable(); // ID of the exhibitor/project
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
            $table->json('metadata')->nullable(); // Additional data like user agent, IP, etc.
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['trackable_type', 'trackable_id']);
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
