<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'MacBook Pro 14 M4',
                'sku' => 'TP-LAP-001',
                'price' => 198000.00,
                'stock' => 12,
                'featured' => true,
                'description' => '14.2-inch Liquid Retina XDR display, Apple M4 chip, 16GB unified memory, 512GB SSD, and all-day battery life for creators and developers.',
                'categories' => ['laptops-computers', 'laptops-computers-ultrabooks-workstations'],
            ],
            [
                'name' => 'ASUS ROG Strix G16 RTX 4070',
                'sku' => 'TP-LAP-002',
                'price' => 185000.00,
                'stock' => 8,
                'featured' => true,
                'description' => '16-inch QHD+ 240Hz gaming laptop powered by Intel Core i9-14900HX, NVIDIA GeForce RTX 4070 8GB, 16GB DDR5 RAM, and 1TB PCIe 4.0 NVMe SSD.',
                'categories' => ['laptops-computers', 'laptops-computers-gaming-laptops'],
            ],
            [
                'name' => 'Dell UltraSharp 27 4K USB-C Hub Monitor',
                'sku' => 'TP-MON-003',
                'price' => 74500.00,
                'stock' => 15,
                'featured' => false,
                'description' => '27-inch 4K IPS Black panel with 98% DCI-P3 color accuracy, 90W USB-C power delivery, and integrated RJ45 Ethernet hub.',
                'categories' => ['laptops-computers', 'laptops-computers-monitors-displays'],
            ],
            [
                'name' => 'Minisforum UM790 Pro Mini PC',
                'sku' => 'TP-MPC-004',
                'price' => 68000.00,
                'stock' => 10,
                'featured' => false,
                'description' => 'Ultra-compact desktop with AMD Ryzen 9 7940HS, Radeon 780M graphics, 32GB DDR5, 1TB NVMe SSD, and dual USB4 ports.',
                'categories' => ['laptops-computers', 'laptops-computers-mini-pcs'],
            ],
            [
                'name' => 'iPhone 16 Pro 256GB',
                'sku' => 'TP-MOB-005',
                'price' => 145000.00,
                'stock' => 18,
                'featured' => true,
                'description' => 'Grade 5 titanium design, A18 Pro chip, 6.3-inch Super Retina XDR ProMotion display, 48MP Fusion camera, and USB-C 10Gbps transfer.',
                'categories' => ['smartphones-tablets', 'smartphones-tablets-flagship-smartphones'],
            ],
            [
                'name' => 'Samsung Galaxy S25 Ultra 512GB',
                'sku' => 'TP-MOB-006',
                'price' => 152000.00,
                'stock' => 14,
                'featured' => true,
                'description' => '6.9-inch Dynamic AMOLED 2X 120Hz display, Snapdragon 8 Elite, built-in S Pen, 200MP quad camera system, and 5000mAh fast-charging battery.',
                'categories' => ['smartphones-tablets', 'smartphones-tablets-flagship-smartphones'],
            ],
            [
                'name' => 'Google Pixel 9a 128GB',
                'sku' => 'TP-MOB-007',
                'price' => 56000.00,
                'stock' => 22,
                'featured' => false,
                'description' => 'Clean Android experience with Google Tensor G4, 6.3-inch Actua OLED display, AI camera tools, and 7 years of OS security updates.',
                'categories' => ['smartphones-tablets', 'smartphones-tablets-mid-range-phones'],
            ],
            [
                'name' => 'iPad Air 11 M3 Wi-Fi 256GB',
                'sku' => 'TP-TAB-008',
                'price' => 78000.00,
                'stock' => 16,
                'featured' => false,
                'description' => '11-inch Liquid Retina display, Apple M3 chip, Apple Pencil Pro support, landscape stereo speakers, and Wi-Fi 6E connectivity.',
                'categories' => ['smartphones-tablets', 'smartphones-tablets-tablets-ipads'],
            ],
            [
                'name' => 'Apple Watch Series 10 46mm',
                'sku' => 'TP-WAT-009',
                'price' => 52000.00,
                'stock' => 20,
                'featured' => false,
                'description' => 'Thinnest Apple Watch with wide-angle OLED display, sleep apnea notifications, ECG, depth gauge, and fast 30-minute charge.',
                'categories' => ['smartphones-tablets', 'smartphones-tablets-smartwatches-wearables'],
            ],
            [
                'name' => 'AMD Ryzen 7 9800X3D Processor',
                'sku' => 'TP-CPU-010',
                'price' => 58500.00,
                'stock' => 11,
                'featured' => true,
                'description' => '8-core, 16-thread Zen 5 desktop processor with 2nd-gen 3D V-Cache technology for top-tier gaming and AM5 platform longevity.',
                'categories' => ['pc-components-hardware', 'pc-components-hardware-processors-cpu'],
            ],
            [
                'name' => 'NVIDIA GeForce RTX 4070 Super 12GB',
                'sku' => 'TP-GPU-011',
                'price' => 84000.00,
                'stock' => 9,
                'featured' => true,
                'description' => 'Ada Lovelace architecture with 12GB GDDR6X memory, DLSS 3.5 Frame Generation, and dual-fan axial cooling for 1440p ultra gaming.',
                'categories' => ['pc-components-hardware', 'pc-components-hardware-graphics-cards-gpu'],
            ],
            [
                'name' => 'ASUS TUF Gaming B650-PLUS WiFi',
                'sku' => 'TP-MBD-012',
                'price' => 26500.00,
                'stock' => 18,
                'featured' => false,
                'description' => 'AM5 ATX motherboard featuring 12+2 teamed power stages, PCIe 5.0 M.2 slot, DDR5 support, Wi-Fi 6, and 2.5Gb Ethernet.',
                'categories' => ['pc-components-hardware', 'pc-components-hardware-motherboards'],
            ],
            [
                'name' => 'Samsung 990 PRO 2TB NVMe SSD',
                'sku' => 'TP-SSD-013',
                'price' => 21500.00,
                'stock' => 30,
                'featured' => false,
                'description' => 'PCIe 4.0 M.2 2280 internal solid state drive delivering up to 7,450 MB/s sequential read speeds for OS, gaming, and 4K editing.',
                'categories' => ['pc-components-hardware', 'pc-components-hardware-ram-storage'],
            ],
            [
                'name' => 'Corsair Vengeance 32GB DDR5-6000 Kit',
                'sku' => 'TP-RAM-014',
                'price' => 14800.00,
                'stock' => 25,
                'featured' => false,
                'description' => '2x16GB DDR5 6000MHz CL30 low-profile memory modules tuned for Intel XMP 3.0 and AMD EXPO overclocking profiles.',
                'categories' => ['pc-components-hardware', 'pc-components-hardware-ram-storage'],
            ],
            [
                'name' => 'Keychron Q1 Pro Wireless Mechanical Keyboard',
                'sku' => 'TP-KBD-015',
                'price' => 22500.00,
                'stock' => 17,
                'featured' => true,
                'description' => '75% CNC aluminum hot-swappable mechanical keyboard with QMK/VIA support, Bluetooth 5.1, double-gasket mount, and PBT keycaps.',
                'categories' => ['peripherals-audio', 'peripherals-audio-mechanical-keyboards'],
            ],
            [
                'name' => 'Logitech G Pro X Superlight 2 Mouse',
                'sku' => 'TP-MSE-016',
                'price' => 17900.00,
                'stock' => 24,
                'featured' => false,
                'description' => '60g ultra-lightweight wireless esports mouse with HERO 2 32K sensor, Lightforce hybrid switches, and 95-hour battery life.',
                'categories' => ['peripherals-audio', 'peripherals-audio-gaming-mice-pads'],
            ],
            [
                'name' => 'Sony WH-1000XM5 Wireless ANC Headphones',
                'sku' => 'TP-AUD-017',
                'price' => 36500.00,
                'stock' => 19,
                'featured' => false,
                'description' => 'Industry-leading multi-microphone active noise cancellation, 30-hour battery life, multipoint Bluetooth, and crystal-clear call quality.',
                'categories' => ['peripherals-audio', 'peripherals-audio-headsets-earbuds'],
            ],
            [
                'name' => 'Anker Prime 140W GaN Charger + USB-C Hub',
                'sku' => 'TP-CHG-018',
                'price' => 11500.00,
                'stock' => 35,
                'featured' => false,
                'description' => 'Compact 4-port GaN wall charger with 140W PD 3.1 fast charging for MacBook Pro, tablets, and smartphones simultaneously.',
                'categories' => ['smart-gadgets-networking', 'smart-gadgets-networking-power-banks-chargers'],
            ],
            [
                'name' => 'TP-Link Archer BE550 Wi-Fi 7 Router',
                'sku' => 'TP-NET-019',
                'price' => 29500.00,
                'stock' => 13,
                'featured' => false,
                'description' => 'Tri-band BE9300 Wi-Fi 7 router with five 2.5Gbps ports, 320MHz channels, Multi-Link Operation, and EasyMesh compatibility.',
                'categories' => ['smart-gadgets-networking', 'smart-gadgets-networking-routers-mesh-wi-fi'],
            ],
            [
                'name' => 'DJI Mini 4 Pro Fly More Drone Combo',
                'sku' => 'TP-DRN-020',
                'price' => 118000.00,
                'stock' => 7,
                'featured' => true,
                'description' => 'Under-249g foldable camera drone with 4K/60fps HDR vertical shooting, omnidirectional obstacle sensing, and 34-minute flight time.',
                'categories' => ['smart-gadgets-networking', 'smart-gadgets-networking-drones-action-cameras'],
            ],
        ];

        $categoryMap = Category::pluck('id', 'slug')->toArray();
        $fallbackCategoryIds = array_values(array_slice($categoryMap, 0, 3));

        foreach ($products as $item) {
            $slug = Str::slug($item['name']);
            $encodedName = str_replace(' ', '+', $item['name']);

            $galleryImages = [];
            for ($j = 1; $j <= 3; $j++) {
                $galleryImages[] = "https://placehold.co/500x500/0f172a/94a3b8?text={$encodedName}+View+{$j}";
            }

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'has_variations' => false,
                    'status' => true,
                    'featured' => $item['featured'],
                    'thumbnail' => "https://placehold.co/500x500/1e293b/38bdf8?text={$encodedName}",
                    'images' => $galleryImages,
                ]
            );

            $attachIds = [];
            foreach ($item['categories'] as $catSlug) {
                if (isset($categoryMap[$catSlug])) {
                    $attachIds[] = $categoryMap[$catSlug];
                }
            }

            if (empty($attachIds)) {
                $attachIds = $fallbackCategoryIds;
            }

            if (!empty($attachIds)) {
                $product->categories()->sync($attachIds);
            }
        }
    }
}
