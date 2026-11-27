<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->string('provider')->default('manual')->after('type');
            $table->string('external_id')->nullable()->after('provider');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_provider')->nullable()->after('shipping_method_id');
            $table->unsignedBigInteger('sendcloud_parcel_id')->nullable()->after('shipping_provider');
            $table->string('tracking_number')->nullable()->after('sendcloud_parcel_id');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->string('shipping_label_url')->nullable()->after('tracking_url');
            $table->string('shipping_status')->nullable()->after('shipping_label_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_provider',
                'sendcloud_parcel_id',
                'tracking_number',
                'tracking_url',
                'shipping_label_url',
                'shipping_status',
            ]);
        });

        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropColumn(['provider', 'external_id']);
        });
    }
};
