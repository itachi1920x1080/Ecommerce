<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            // បង្កើតឈ្មោះទំនិញក្លែងក្លាយ (២ ទៅ ៣ ពាក្យ)
            'name' => ucfirst($this->faker->words(3, true)),
            
            // បង្កើតតម្លៃចន្លោះពី 5.00$ ដល់ 150.00$
            'price' => $this->faker->randomFloat(2, 5, 150),
            
            // បង្កើតចំនួនស្តុកចន្លោះពី 10 ដល់ 100
            'stock' => $this->faker->numberBetween(10, 100),
            
            // បង្កើតការពិពណ៌នាខ្លីៗ
            'description' => $this->faker->paragraph(),

            // Add random discount to some products (30% chance)
            'discount_percent' => $this->faker->boolean(30) ? $this->faker->numberBetween(10, 50) : 0,
            
            // ទាញយករូបភាព Random ពី Internet យកមកធ្វើតេស្ត
            'image_url' => 'https://picsum.photos/400/400?random=' . $this->faker->unique()->numberBetween(1, 1000),
        ];
    }
}