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
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000, // Base Currency
                'active' => true,
            ],
            [
                'code' => 'BDT',
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'exchange_rate' => 120.0000, // Example Rate
                'active' => true,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.9200,
                'active' => true,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'exchange_rate' => 1.3500,
                'active' => true,
            ],
            [
                'code' => 'MYR',
                'name' => 'Malaysian Ringgit',
                'symbol' => 'RM',
                'exchange_rate' => 4.7500,
                'active' => true,
            ],
            [
                'code' => 'SGD',
                'name' => 'Singapore Dollar',
                'symbol' => 'S$',
                'exchange_rate' => 1.3400,
                'active' => true,
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
