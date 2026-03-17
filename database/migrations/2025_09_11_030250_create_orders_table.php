<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            
            $table->string('order_number')->unique();
            $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled

            $table->string('currency_code', 3);
            $table->foreign('currency_code')->references('code')->on('currencies');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->foreignId('shipping_method_id')->nullable()->constrained('shipping_methods')->restrictOnDelete();
            
            // 4. Payment Snapshot
            $table->string('payment_method')->default('cod'); // e.g. 'stripe', 'cod'
            $table->string('payment_status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};