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
        $sampleNames = [
            'Alpha Focus',
            'Nova Blend',
            'Urban Craft',
            'Prime Select',
            'Eco Choice',
            'Fresh Line',
            'Daily Edge',
            'Golden Leaf',
            'Pure Touch',
            'Modern Nest',
            'Swift Move',
            'Classic Stone',
            'Bright Path',
            'Silver Bay',
            'Blue Orbit',
            'Vista Point',
            'Amber Ridge',
            'Core Pulse',
            'Luxe Harbor',
            'True North',
        ];

        $categoryIds = Category::pluck('id')->toArray();

        for ($i = 1; $i <= 20; $i++) {
            $name = $sampleNames[$i - 1] ?? ('Sample Product '.$i);
            $slug = Str::slug($name);
            $description = 'A reliable product designed for everyday store use and easy presentation.';
            $price = 19.99 + ($i * 4.25);
            $stock = 10 + ($i * 2);
            
            // Create Dummy Gallery Images
            $galleryImages = [];
            for ($j = 1; $j <= 3; $j++) {
                $galleryImages[] = "https://placehold.co/500x500/f1f5f9/475569?text={$name}+View+{$j}";
            }

            // Create Product
            $product = Product::create([
                'name' => $name,
                'slug' => $slug,
                'sku' => uniqid(),
                'description' => $description,
                'price' => round($price, 2),
                'stock' => $stock,
                'has_variations' => false,
                'status' => true,
                'featured' => $i % 3 === 0,
                'thumbnail' => "https://placehold.co/500x500/e2e8f0/1e293b?text=" . str_replace(' ', '+', $name),
                'images' => $galleryImages,
            ]);

            if (!empty($categoryIds)) {
                $product->categories()->attach(
                    array_slice($categoryIds, 0, min(3, count($categoryIds)))
                );
            }
        }
    }
}
