<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->enum('sale_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('sale_value', 10, 2)->default(0);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('flash_sale_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained('flash_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['flash_sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_product');
        Schema::dropIfExists('flash_sales');
    }
};
