<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

/**
 * HU-04: Registro de productos con costo y precio
 *
 * Replica los datos de referencia (SEED) del script SQL:
 * dss_repuestos_automotrices -> tabla product_categories.
 */
class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Frenos', 'description' => 'Pastillas, discos, tambores y componentes del sistema de frenos'],
            ['name' => 'Filtros', 'description' => 'Filtros de aceite, aire, combustible y habitáculo'],
            ['name' => 'Motor', 'description' => 'Bujías, correas, juntas, pistones y componentes internos del motor'],
            ['name' => 'Suspensión', 'description' => 'Amortiguadores, resortes, rótulas y bujes de suspensión'],
            ['name' => 'Eléctrico', 'description' => 'Sensores, baterías, alternadores y componentes eléctricos'],
            ['name' => 'Transmisión', 'description' => 'Embragues, cajas de cambio y componentes de transmisión'],
            ['name' => 'Enfriamiento', 'description' => 'Radiadores, termostatos, mangueras y sistema de refrigeración'],
            ['name' => 'Carrocería', 'description' => 'Espejos, manijas, faros y piezas de carrocería'],
            ['name' => 'Lubricantes', 'description' => 'Aceites de motor, transmisión y líquidos especiales'],
            ['name' => 'Escape', 'description' => 'Silenciadores, catalizadores y componentes del sistema de escape'],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
