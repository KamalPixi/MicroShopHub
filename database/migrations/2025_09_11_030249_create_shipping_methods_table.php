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
         * Shipping Methods
         */
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g., Standard, Express
            $table->decimal('cost', 10, 2)->default(0); // flat cost
            $table->integer('estimated_days')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
