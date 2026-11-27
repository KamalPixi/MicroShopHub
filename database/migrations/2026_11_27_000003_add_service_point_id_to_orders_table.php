<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('sendcloud_service_point_id')->nullable()->after('sendcloud_parcel_id');
            $table->string('sendcloud_service_point_name')->nullable()->after('sendcloud_service_point_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['sendcloud_service_point_id', 'sendcloud_service_point_name']);
        });
    }
};
