<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first
        $categories = ['Skincare', 'Makeup', 'Haircare', 'Fragrance', 'Body & Bath', 'Tools & Accessories'];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // Create 60 products per category = 360 total ✅
        Category::all()->each(function ($category) {
            Product::factory(60)->create([
                'category_id' => $category->id,
            ]);
        });
    }
}