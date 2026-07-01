<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 5, 200);

        return [
            'sku' => 'SKU-'.fake()->unique()->numerify('#####'),
            'name' => fake()->words(3, true),
            'brand_id' => Brand::factory(),
            'category_id' => null,
            'compatibility' => null,
            'price' => $cost + fake()->randomFloat(2, 1, 50),
            'cost' => $cost,
            'stock' => fake()->numberBetween(0, 100),
            'stock_min' => 5,
            'estado' => true,
        ];
    }
}
