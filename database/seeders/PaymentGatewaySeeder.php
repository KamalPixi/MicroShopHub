<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Cash on Delivery',
                'code' => 'cod',
                'description' => 'Pay with cash upon delivery.',
                'is_active' => true,
                'sort_order' => 1,
                'config' => null,
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'description' => 'Pay securely with Credit Card.',
                'is_active' => true,
                'sort_order' => 2,
                'config' => json_encode(['public_key' => '', 'secret_key' => '']),
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'Pay with your PayPal account.',
                'is_active' => true,
                'sort_order' => 3,
                'config' => json_encode(['client_id' => '', 'secret' => '']),
            ],
            [
                'name' => 'SSLCommerz',
                'code' => 'sslcommerz',
                'description' => 'Pay with Local Bank Cards / Mobile Banking.',
                'is_active' => true,
                'sort_order' => 4,
                'config' => json_encode(['store_id' => '', 'store_password' => '']),
            ],
            [
                'name' => 'PortPos',
                'code' => 'portpos',
                'description' => 'Invoice-based checkout via PortPos.',
                'is_active' => true,
                'sort_order' => 5,
                'config' => json_encode(['app_key' => '', 'secret_key' => '', 'sandbox' => false]),
            ],
        ];

        DB::table('payment_gateways')->insert($gateways);
    }
}
