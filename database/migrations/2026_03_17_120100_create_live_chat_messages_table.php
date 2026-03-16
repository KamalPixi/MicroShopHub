<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('live_chat_sessions')->cascadeOnDelete();
            $table->string('sender'); // customer|admin
            $table->text('message');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
    }
};
