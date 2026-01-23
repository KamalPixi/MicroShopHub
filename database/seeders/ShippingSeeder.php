<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ShippingZone;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Zones
        $bdZone = ShippingZone::create(['name' => 'Bangladesh (Domestic)']);
        $asiaZone = ShippingZone::create(['name' => 'Asia']);
        $worldZone = ShippingZone::create(['name' => 'Rest of World']);

        // 2. Assign Countries to Zones
        DB::table('shipping_zone_locations')->insert([
            // Domestic
            ['shipping_zone_id' => $bdZone->id, 'country_code' => 'BD', 'state_code' => null],
            
            // Asia (Examples)
            ['shipping_zone_id' => $asiaZone->id, 'country_code' => 'SG', 'state_code' => null], // Singapore
            ['shipping_zone_id' => $asiaZone->id, 'country_code' => 'MY', 'state_code' => null], // Malaysia
            ['shipping_zone_id' => $asiaZone->id, 'country_code' => 'IN', 'state_code' => null], // India
            
            // Rest of World (Examples)
            ['shipping_zone_id' => $worldZone->id, 'country_code' => 'US', 'state_code' => null],
            ['shipping_zone_id' => $worldZone->id, 'country_code' => 'CA', 'state_code' => null],
            ['shipping_zone_id' => $worldZone->id, 'country_code' => 'GB', 'state_code' => null], // UK
        ]);

        // 3. Create Shipping Methods
        DB::table('shipping_methods')->insert([
            // BD Methods
            [
                'shipping_zone_id' => $bdZone->id,
                'name' => 'Inside Dhaka',
                'type' => 'flat_rate',
                'cost' => 60.00, // 60 Taka (based on default currency of the system)
                'estimated_days' => 2,
                'active' => true
            ],
            [
                'shipping_zone_id' => $bdZone->id,
                'name' => 'Outside Dhaka',
                'type' => 'flat_rate',
                'cost' => 120.00,
                'estimated_days' => 5,
                'active' => true
            ],

            // Asia Methods
            [
                'shipping_zone_id' => $asiaZone->id,
                'name' => 'Asia Standard',
                'type' => 'flat_rate',
                'cost' => 15.00, // $15 USD
                'estimated_days' => 10,
                'active' => true
            ],

            // World Methods
            [
                'shipping_zone_id' => $worldZone->id,
                'name' => 'DHL Express',
                'type' => 'flat_rate',
                'cost' => 45.00, // $45 USD
                'estimated_days' => 5,
                'active' => true
            ]
        ]);
    }
}
