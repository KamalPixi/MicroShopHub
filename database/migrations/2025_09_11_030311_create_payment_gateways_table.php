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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Stripe", "PayPal", "Cash on Delivery"
            $table->string('code')->unique(); // e.g., "stripe", "cod"
            $table->text('description')->nullable();
            
            // Store API Keys securely (consider encryption in Model)
            $table->json('config')->nullable(); // { "public_key": "...", "secret": "..." }
            
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
