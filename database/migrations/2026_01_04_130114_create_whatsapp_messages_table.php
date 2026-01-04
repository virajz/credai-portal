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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('channel');
            $table->string('from');
            $table->string('to');
            $table->string('sender_name');
            $table->string('content_type');
            $table->text('text')->nullable();
            $table->json('raw_payload');
            $table->timestamp('received_at');
            $table->boolean('thank_you_sent')->default(false);
            $table->timestamps();

            $table->index('from');
            $table->index('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
