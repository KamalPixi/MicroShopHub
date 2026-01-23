<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Shipping Zones (e.g., "Domestic", "North America", "Europe")
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Zone Locations (Which countries belong to which zone)
        Schema::create('shipping_zone_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->char('country_code', 2); // e.g., 'US', 'CA'
            $table->string('state_code')->nullable(); // Optional: specific states
            $table->timestamps();
        });

        // 3. Shipping Methods (Linked to Zones)
        // Replaces your old 'shipping_methods' table
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "DHL Express", "Standard"
            $table->string('type')->default('flat_rate'); // flat_rate, free_shipping, local_pickup
            $table->decimal('cost', 12, 2)->default(0);
            $table->integer('estimated_days')->default(0);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zone_locations');
        Schema::dropIfExists('shipping_zones');
    }
};
