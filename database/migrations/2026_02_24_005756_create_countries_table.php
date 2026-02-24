<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->char('code', 2)->primary();
            $table->string('name', 120);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('countries')->insert([
            ['code' => 'BD', 'name' => 'Bangladesh', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'US', 'name' => 'United States', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GB', 'name' => 'United Kingdom', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CA', 'name' => 'Canada', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'MY', 'name' => 'Malaysia', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SG', 'name' => 'Singapore', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
