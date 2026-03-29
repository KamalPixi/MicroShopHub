<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $countries = [
            ['code' => 'BD', 'name' => 'Bangladesh', 'active' => true],
            ['code' => 'US', 'name' => 'United States', 'active' => true],
            ['code' => 'GB', 'name' => 'United Kingdom', 'active' => true],
            ['code' => 'CA', 'name' => 'Canada', 'active' => true],
            ['code' => 'MY', 'name' => 'Malaysia', 'active' => true],
            ['code' => 'SG', 'name' => 'Singapore', 'active' => true],
            ['code' => 'IN', 'name' => 'India', 'active' => true],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'active' => true],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'active' => true],
            ['code' => 'AU', 'name' => 'Australia', 'active' => true],
            ['code' => 'NZ', 'name' => 'New Zealand', 'active' => true],
            ['code' => 'DE', 'name' => 'Germany', 'active' => true],
            ['code' => 'FR', 'name' => 'France', 'active' => true],
            ['code' => 'NL', 'name' => 'Netherlands', 'active' => true],
            ['code' => 'JP', 'name' => 'Japan', 'active' => true],
            ['code' => 'KR', 'name' => 'South Korea', 'active' => true],
            ['code' => 'CN', 'name' => 'China', 'active' => true],
            ['code' => 'TH', 'name' => 'Thailand', 'active' => true],
            ['code' => 'PH', 'name' => 'Philippines', 'active' => true],
            ['code' => 'ID', 'name' => 'Indonesia', 'active' => true],
            ['code' => 'VN', 'name' => 'Vietnam', 'active' => true],
            ['code' => 'PK', 'name' => 'Pakistan', 'active' => true],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'active' => true],
            ['code' => 'NP', 'name' => 'Nepal', 'active' => true],
        ];

        foreach ($countries as &$country) {
            $country['created_at'] = $now;
            $country['updated_at'] = $now;
        }
        unset($country);

        DB::table('countries')->upsert(
            $countries,
            ['code'],
            ['name', 'active', 'updated_at']
        );

        Country::query()->where('code', 'BD')->update(['active' => true]);
    }
}
