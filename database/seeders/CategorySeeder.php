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
        // Define the category hierarchy
        $categories = [
            [
                'name' => 'Men\'s Fashion',
                'subtitle' => 'Modern styles for him',
                // 400x225 is a 16:9 aspect ratio
                'thumbnail' => 'https://via.placeholder.com/400x225/3b82f6/ffffff?text=Mens+Fashion',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'T-Shirts & Polos', 'subtitle' => 'Casual essentials'],
                    ['name' => 'Shirts', 'subtitle' => 'Formal & Casual'],
                    ['name' => 'Jeans & Trousers', 'subtitle' => 'Denim and chinos'],
                    ['name' => 'Jackets & Coats', 'subtitle' => 'Outerwear for all seasons'],
                    ['name' => 'Activewear', 'subtitle' => 'Gym and sports gear'],
                ],
            ],
            [
                'name' => 'Women\'s Fashion',
                'subtitle' => 'Trending styles for her',
                'thumbnail' => 'https://via.placeholder.com/400x225/ec4899/ffffff?text=Womens+Fashion',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Dresses', 'subtitle' => 'Elegant and casual dresses'],
                    ['name' => 'Tops & Blouses', 'subtitle' => 'Versatile upper wear'],
                    ['name' => 'Skirts & Shorts', 'subtitle' => 'Summer favorites'],
                    ['name' => 'Handbags', 'subtitle' => 'Stylish carriers'],
                    ['name' => 'Footwear', 'subtitle' => 'Heels, flats, and sneakers'],
                ],
            ],
            [
                'name' => 'Kids\' Wear',
                'subtitle' => 'Comfortable clothes for kids',
                'thumbnail' => 'https://via.placeholder.com/400x225/f59e0b/ffffff?text=Kids+Wear',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Boys\' Clothing', 'subtitle' => 'Tough and playful'],
                    ['name' => 'Girls\' Clothing', 'subtitle' => 'Cute and colorful'],
                    ['name' => 'Baby Clothing', 'subtitle' => 'Soft and gentle'],
                ],
            ],
            [
                'name' => 'Accessories',
                'subtitle' => 'Complete your look',
                'thumbnail' => 'https://via.placeholder.com/400x225/10b981/ffffff?text=Accessories',
                'show_on_homepage' => false,
                'children' => [
                    ['name' => 'Watches', 'subtitle' => 'Timeless pieces'],
                    ['name' => 'Sunglasses', 'subtitle' => 'Eye protection & style'],
                    ['name' => 'Belts & Wallets', 'subtitle' => 'Leather goods'],
                    ['name' => 'Jewelry', 'subtitle' => 'Necklaces, rings & more'],
                ],
            ],
            [
                'name' => 'Footwear',
                'subtitle' => 'Step out in style',
                'thumbnail' => 'https://via.placeholder.com/400x225/6366f1/ffffff?text=Footwear',
                'show_on_homepage' => true,
                'children' => [
                    ['name' => 'Sneakers', 'subtitle' => 'Everyday comfort'],
                    ['name' => 'Formal Shoes', 'subtitle' => 'Business & events'],
                    ['name' => 'Boots', 'subtitle' => 'Rugged and stylish'],
                ],
            ],
        ];

        foreach ($categories as $parentData) {
            // Create Parent Category
            $parent = Category::create([
                'name' => $parentData['name'],
                'slug' => Str::slug($parentData['name']),
                'subtitle' => $parentData['subtitle'],
                'thumbnail' => $parentData['thumbnail'],
                'show_on_homepage' => $parentData['show_on_homepage'],
                'parent_id' => null,
            ]);

            // Create Children Categories
            if (isset($parentData['children'])) {
                foreach ($parentData['children'] as $childData) {
                    Category::create([
                        'name' => $childData['name'],
                        'slug' => Str::slug($childData['name']),
                        'subtitle' => $childData['subtitle'],
                        'thumbnail' => null, 
                        'show_on_homepage' => false,
                        'parent_id' => $parent->id,
                    ]);
                }
            }
        }
    }
}
