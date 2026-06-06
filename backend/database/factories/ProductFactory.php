<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $category = Category::inRandomOrder()->first();

        $products = match($category?->name) {
            'Skincare' => [
                'name' => $this->faker->randomElement([
                    'Hydrating Face Serum', 'Vitamin C Brightening Cream',
                    'Gentle Foam Cleanser', 'Retinol Night Cream',
                    'SPF 50 Sunscreen', 'Hyaluronic Acid Toner',
                    'Niacinamide Serum', 'Rose Hip Face Oil',
                    'Exfoliating Face Scrub', 'Collagen Eye Cream',
                    'Aloe Vera Gel', 'Micellar Cleansing Water',
                ]),
                'image_url' => $this->faker->randomElement([
                    'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=400',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=400',
                    'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=400',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400',
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=400',
                    'https://images.unsplash.com/photo-1612817288484-6f916006741a?w=400',
                ]),
            ],
            'Makeup' => [
                'name' => $this->faker->randomElement([
                    'Matte Lipstick', 'Volumizing Mascara', 'HD Foundation',
                    'Eyeshadow Palette', 'Liquid Eyeliner', 'Highlighter Powder',
                    'Contour Kit', 'Setting Spray', 'BB Cream SPF30', 'Lip Gloss',
                ]),
                'image_url' => $this->faker->randomElement([
                    'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=400',
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400',
                    'https://images.unsplash.com/photo-1583241475880-083f84372725?w=400',
                    'https://images.unsplash.com/photo-1631214524020-3c69f1eded39?w=400',
                ]),
            ],
            'Haircare' => [
                'name' => $this->faker->randomElement([
                    'Argan Oil Shampoo', 'Deep Repair Conditioner',
                    'Hair Growth Serum', 'Keratin Hair Mask',
                    'Scalp Treatment Oil', 'Anti-Frizz Spray',
                ]),
                'image_url' => $this->faker->randomElement([
                    'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?w=400',
                    'https://images.unsplash.com/photo-1526045612212-70caf35c14df?w=400',
                    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400',
                ]),
            ],
            'Fragrance' => [
                'name' => $this->faker->randomElement([
                    'Rose Perfume 50ml', 'Oud Cologne 100ml',
                    'Jasmine Body Mist', 'Lavender Eau de Toilette',
                    'Vanilla Perfume 30ml', 'Floral Eau de Parfum',
                ]),
                'image_url' => $this->faker->randomElement([
                    'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400',
                    'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?w=400',
                    'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400',
                ]),
            ],
            default => [
                'name' => $this->faker->randomElement([
                    'Beauty Essentials Kit', 'Luxury Spa Set',
                    'Daily Care Bundle', 'Premium Beauty Set',
                ]),
                'image_url' => $this->faker->randomElement([
                    'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=400',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=400',
                ]),
            ],
        };

        return [
            'name'             => $products['name'],
            'price'            => $this->faker->randomFloat(2, 5, 150),
            'stock'            => $this->faker->numberBetween(10, 100),
            'description'      => $this->faker->paragraph(),
            'discount_percent' => $this->faker->boolean(30) ? $this->faker->numberBetween(10, 50) : 0,
            'image_url'        => $products['image_url'],
            'category_id'      => $category?->id,
        ];
    }
}