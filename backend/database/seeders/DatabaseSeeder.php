<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
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
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'), // លេខសម្ងាត់គឺ password123
            'role' => 'admin',
        ]);

        // ២. បង្កើតគណនី User ធម្មតា សម្រាប់ Test
        User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
        User::create([
            'name' => 'Driver Test',
            'email' => 'driver@test.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
        ]);
        // ៣. បង្កើតទំនិញសាកល្បង ២០ មុខមកវិញ (បើមិនចង់បាន អាចលុបបន្ទាត់នេះចោលបាន)
        Product::factory(20)->create();
    }
}