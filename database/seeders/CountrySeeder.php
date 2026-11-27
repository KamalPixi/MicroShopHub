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
            ['code' => 'AT', 'name' => 'Austria', 'active' => true],
            ['code' => 'BE', 'name' => 'Belgium', 'active' => true],
            ['code' => 'BG', 'name' => 'Bulgaria', 'active' => true],
            ['code' => 'HR', 'name' => 'Croatia', 'active' => true],
            ['code' => 'CZ', 'name' => 'Czechia', 'active' => true],
            ['code' => 'DK', 'name' => 'Denmark', 'active' => true],
            ['code' => 'EE', 'name' => 'Estonia', 'active' => true],
            ['code' => 'FI', 'name' => 'Finland', 'active' => true],
            ['code' => 'FR', 'name' => 'France', 'active' => true],
            ['code' => 'DE', 'name' => 'Germany', 'active' => true],
            ['code' => 'GR', 'name' => 'Greece', 'active' => true],
            ['code' => 'HU', 'name' => 'Hungary', 'active' => true],
            ['code' => 'IS', 'name' => 'Iceland', 'active' => true],
            ['code' => 'IT', 'name' => 'Italy', 'active' => true],
            ['code' => 'LV', 'name' => 'Latvia', 'active' => true],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'active' => true],
            ['code' => 'LT', 'name' => 'Lithuania', 'active' => true],
            ['code' => 'LU', 'name' => 'Luxembourg', 'active' => true],
            ['code' => 'MT', 'name' => 'Malta', 'active' => true],
            ['code' => 'NL', 'name' => 'Netherlands', 'active' => true],
            ['code' => 'NO', 'name' => 'Norway', 'active' => true],
            ['code' => 'PL', 'name' => 'Poland', 'active' => true],
            ['code' => 'PT', 'name' => 'Portugal', 'active' => true],
            ['code' => 'RO', 'name' => 'Romania', 'active' => true],
            ['code' => 'SK', 'name' => 'Slovakia', 'active' => true],
            ['code' => 'SI', 'name' => 'Slovenia', 'active' => true],
            ['code' => 'ES', 'name' => 'Spain', 'active' => true],
            ['code' => 'SE', 'name' => 'Sweden', 'active' => true],
            ['code' => 'CH', 'name' => 'Switzerland', 'active' => true],
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

        Country::query()
            ->whereNotIn('code', array_column($countries, 'code'))
            ->update(['active' => false]);
    }
}
