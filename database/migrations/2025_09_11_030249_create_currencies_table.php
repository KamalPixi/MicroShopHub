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
        Schema::create('currencies', function (Blueprint $table) {
            // We use the code as the primary key because it is unique globally
            $table->string('code', 3)->primary(); // 'USD', 'BDT', 'EUR'
            $table->string('name');               // 'US Dollar'
            $table->string('symbol');             // '$', '৳'
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); 
            $table->boolean('active')->default(true);
            // Flag to identify the main store currency
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
