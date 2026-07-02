<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NOTA: esta factory ya debería existir si HU-07 fue integrada primero
 * (ahí se crea junto al resto del módulo de ventas). Se incluye aquí
 * también para que los tests de HU-08/HU-09 puedan correr de forma
 * autocontenida si se prueban antes de fusionar con HU-07. Si ya existe
 * en el proyecto, no la dupliques.
 */
class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10, 100);
        $unitCost = $unitPrice * 0.6;

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_cost' => round($unitCost, 2),
            'subtotal' => round($unitPrice * $quantity, 2),
        ];
    }
}
