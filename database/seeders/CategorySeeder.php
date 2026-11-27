<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptops & Computers',
                'subtitle' => 'Workstations, gaming rigs, and ultrabooks',
                'thumbnail' => 'https://placehold.co/400x225/0f172a/38bdf8?text=Laptops+%26+Computers',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Gaming Laptops', 'subtitle' => 'High-refresh RTX & Ryzen machines'],
                    ['name' => 'Ultrabooks & Workstations', 'subtitle' => 'Thin, light, and studio-ready'],
                    ['name' => 'Desktop PCs', 'subtitle' => 'Custom & pre-built towers'],
                    ['name' => 'Mini PCs', 'subtitle' => 'Compact powerhouses'],
                    ['name' => 'Monitors & Displays', 'subtitle' => '4K, OLED, and 144Hz+ panels'],
                ],
            ],
            [
                'name' => 'Smartphones & Tablets',
                'subtitle' => 'Flagship phones, tablets, and wearables',
                'thumbnail' => 'https://placehold.co/400x225/1e293b/818cf8?text=Smartphones+%26+Tablets',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Flagship Smartphones', 'subtitle' => 'Pro cameras and 5G performance'],
                    ['name' => 'Mid-Range Phones', 'subtitle' => 'Everyday value and battery life'],
                    ['name' => 'Tablets & iPads', 'subtitle' => 'Productivity and digital art'],
                    ['name' => 'Smartwatches & Wearables', 'subtitle' => 'Fitness and health tracking'],
                    ['name' => 'Cases & Screen Protectors', 'subtitle' => 'MagSafe & tempered glass'],
                ],
            ],
            [
                'name' => 'PC Components & Hardware',
                'subtitle' => 'Build and upgrade your rig',
                'thumbnail' => 'https://placehold.co/400x225/18181b/22c55e?text=PC+Hardware',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Processors (CPU)', 'subtitle' => 'Intel Core & AMD Ryzen'],
                    ['name' => 'Graphics Cards (GPU)', 'subtitle' => 'NVIDIA GeForce & AMD Radeon'],
                    ['name' => 'Motherboards', 'subtitle' => 'ATX, mATX, and Mini-ITX boards'],
                    ['name' => 'RAM & Storage', 'subtitle' => 'DDR5 memory and NVMe SSDs'],
                    ['name' => 'Power Supplies & Cooling', 'subtitle' => 'Modular PSUs and AIO coolers'],
                ],
            ],
            [
                'name' => 'Peripherals & Audio',
                'subtitle' => 'Keyboards, mice, and studio sound',
                'thumbnail' => 'https://placehold.co/400x225/111827/f59e0b?text=Peripherals+%26+Audio',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Mechanical Keyboards', 'subtitle' => 'Hot-swappable and wireless'],
                    ['name' => 'Gaming Mice & Pads', 'subtitle' => 'Lightweight precision sensors'],
                    ['name' => 'Headsets & Earbuds', 'subtitle' => 'ANC and spatial audio'],
                    ['name' => 'Webcams & Microphones', 'subtitle' => '4K streaming and podcast gear'],
                    ['name' => 'Hubs, Docks & Cables', 'subtitle' => 'Thunderbolt, USB-C, and HDMI'],
                ],
            ],
            [
                'name' => 'Smart Gadgets & Networking',
                'subtitle' => 'Connected tech, power, and Wi-Fi',
                'thumbnail' => 'https://placehold.co/400x225/09090b/ec4899?text=Smart+Gadgets',
                'show_on_homepage' => false,
                'children' => [
                    ['name' => 'Routers & Mesh Wi-Fi', 'subtitle' => 'Wi-Fi 6E & Wi-Fi 7 coverage'],
                    ['name' => 'Power Banks & Chargers', 'subtitle' => 'GaN fast charging solutions'],
                    ['name' => 'Smart Home Devices', 'subtitle' => 'Lights, plugs, and cameras'],
                    ['name' => 'Drones & Action Cameras', 'subtitle' => '4K stabilized capture'],
                ],
            ],
        ];

        foreach ($categories as $parentData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentData['name'])],
                [
                    'name' => $parentData['name'],
                    'subtitle' => $parentData['subtitle'],
                    'thumbnail' => $parentData['thumbnail'],
                    'show_on_homepage' => $parentData['show_on_homepage'],
                    'is_active' => true,
                    'parent_id' => null,
                ]
            );

            if (isset($parentData['children'])) {
                foreach ($parentData['children'] as $childData) {
                    $uniqueSlug = Str::slug($parent->name . ' ' . $childData['name']);

                    Category::updateOrCreate(
                        ['slug' => $uniqueSlug],
                        [
                            'name' => $childData['name'],
                            'subtitle' => $childData['subtitle'],
                            'thumbnail' => null,
                            'show_on_homepage' => false,
                            'is_active' => true,
                            'parent_id' => $parent->id,
                        ]
                    );
                }
            }
        }
    }
}
