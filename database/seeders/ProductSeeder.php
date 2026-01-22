<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categoryIds = Category::pluck('id')->toArray();

        for ($i = 1; $i <= 20; $i++) {
            $name = ucwords($faker->unique()->words(rand(2, 3), true));
            $slug = Str::slug($name);
            
            // Create Dummy Gallery Images
            $galleryImages = [];
            for ($j = 1; $j <= 3; $j++) {
                $galleryImages[] = "https://placehold.co/500x500/f1f5f9/475569?text={$name}+View+{$j}";
            }

            // Create Product
            $product = Product::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $faker->paragraph(3),
                'price' => $faker->randomFloat(2, 10, 300),
                'stock' => $faker->numberBetween(5, 50),
                'has_variations' => false,
                'status' => true,
                'featured' => $faker->boolean(30),
                'thumbnail' => "https://placehold.co/500x500/e2e8f0/1e293b?text=" . str_replace(' ', '+', $name),
                'images' => $galleryImages,
            ]);

            if (!empty($categoryIds)) {
                $product->categories()->attach(
                    $faker->randomElements($categoryIds, rand(1, 3))
                );
            }
        }
    }
}
