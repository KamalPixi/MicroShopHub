<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'BDT',
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'exchange_rate' => 1.0000,
                'active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 120.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 130.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 150.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'exchange_rate' => 88.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'exchange_rate' => 78.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'MYR',
                'name' => 'Malaysian Ringgit',
                'symbol' => 'RM',
                'exchange_rate' => 27.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'SGD',
                'name' => 'Singapore Dollar',
                'symbol' => 'S$',
                'exchange_rate' => 90.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'exchange_rate' => 1.4000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'symbol' => 'د.إ',
                'exchange_rate' => 32.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'symbol' => '﷼',
                'exchange_rate' => 32.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'PKR',
                'name' => 'Pakistani Rupee',
                'symbol' => '₨',
                'exchange_rate' => 0.4300,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'exchange_rate' => 17.0000,
                'active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'exchange_rate' => 0.8500,
                'active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
