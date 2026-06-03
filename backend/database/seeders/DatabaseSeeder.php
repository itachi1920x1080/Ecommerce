<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <--- ត្រូវបន្ថែមបន្ទាត់នេះជាដាច់ខាត

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // ១. បង្កើតគណនី Admin សម្រាប់ Test
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password123'), // លេខសម្ងាត់គឺ password123
                'role' => 'admin',
            ]
        );

        // ២. បង្កើតគណនី User ធម្មតា សម្រាប់ Test
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'User Test',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
        User::firstOrCreate(
            ['email' => 'driver@test.com'],
            [
                'name' => 'Driver Test',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ]
        );
        // ៣. បង្កើត Categories
        $categories = ['Skincare', 'Makeup', 'Body & Bath', 'Fragrance', 'Haircare', 'Tools & Accessories'];
        foreach ($categories as $cat) {
            Category::firstOrCreate([
                'name' => $cat,
                'slug' => strtolower(str_replace([' & ', ' '], '-', $cat))
            ]);
        }
        
        // ៤. បង្កើតទំនិញសាកល្បង ២០ មុខមកវិញ (បើមិនចង់បាន អាចលុបបន្ទាត់នេះចោលបាន)
        // យើងនឹងចាត់វាចូលទៅកាន់ Category ដោយស្វ័យប្រវត្តិ
        Product::factory(20)->create()->each(function ($product) {
            $product->update(['category_id' => Category::inRandomOrder()->first()->id]);
        });
    }
}