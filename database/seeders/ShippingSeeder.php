<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $shopZone = ShippingZone::firstOrCreate(['name' => 'In-Store Pickup']);

        ShippingMethod::updateOrCreate(
            ['name' => 'Collect from Shop'],
            [
                'shipping_zone_id' => $shopZone->id,
                'type' => 'local_pickup',
                'provider' => 'manual',
                'cost' => 0.00,
                'estimated_days' => 0,
                'active' => true,
            ]
        );
    }
}
