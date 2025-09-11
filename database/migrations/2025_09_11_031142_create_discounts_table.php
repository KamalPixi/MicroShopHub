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
        /**
         * Discounts / Coupons
         */
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // e.g., SAVE10
            $table->string('type');                  // percentage, fixed, free_shipping
            $table->decimal('value', 10, 2)->nullable(); // discount amount or %
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('usage_limit')->nullable(); // total usage
            $table->integer('per_user_limit')->nullable(); // per user
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        /**
         * Order <-> Discount (pivot, since one order can have multiple discounts)
         */
        Schema::create('discount_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('applied_value', 10, 2)->default(0); // how much was discounted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('discount_order');
    }
};
