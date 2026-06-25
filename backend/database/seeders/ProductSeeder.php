<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Asegurar la existencia de las categorías necesarias en la base de datos
        $categories = ['Brakes', 'Filters', 'Engine', 'Suspension', 'Electrical', 'Transmission', 'Cooling'];
        foreach ($categories as $catName) {
            ProductCategory::firstOrCreate(
                ['name' => $catName],
                ['description' => "Automotive components for $catName systems.", 'estado' => 1]
            );
        }

        // 2. Asegurar la existencia de las marcas necesarias en la base de datos
        $brands = [
            ['name' => 'Bosch', 'country' => 'Germany'],
            ['name' => 'NGK', 'country' => 'Japan'],
            ['name' => 'Monroe', 'country' => 'Belgium'],
            ['name' => 'Gates', 'country' => 'United States']
        ];
        foreach ($brands as $brandData) {
            Brand::firstOrCreate(
                ['name' => $brandData['name']],
                ['country' => $brandData['country'], 'estado' => 1]
            );
        }

        // 3. Ahora que las tablas tienen datos reales, recuperamos sus IDs de forma segura
        $brandIds = Brand::pluck('id')->toArray();
        $categoryIds = ProductCategory::pluck('id')->toArray();

        // 4. Listado manual de productos de prueba
        $rawProducts = [
            ['name' => 'Pastillas de Freno Delanteras Premium', 'cost' => 25.00, 'price' => 45.00],
            ['name' => 'Filtro de Aceite Sintético', 'cost' => 5.50, 'price' => 12.00],
            ['name' => 'Bujía Iridium de Alta Eficiencia', 'cost' => 3.20, 'price' => 8.50],
            ['name' => 'Amortiguador Hidráulico Delantero', 'cost' => 40.00, 'price' => 75.00],
            ['name' => 'Bomba de Agua para Motor 2.0', 'cost' => 30.00, 'price' => 58.00],
            ['name' => 'Kit de Embrague Completo', 'cost' => 110.00, 'price' => 195.00],
            ['name' => 'Correa de Distribución Reforzada', 'cost' => 15.00, 'price' => 32.00],
            ['name' => 'Radiador de Enfriamiento de Aluminio', 'cost' => 55.00, 'price' => 98.00],
            ['name' => 'Disco de Freno Ventilado', 'cost' => 22.00, 'price' => 42.00],
            ['name' => 'Filtro de Aire de Habitáculo', 'cost' => 6.00, 'price' => 14.50],
        ];

        // 5. Insertar secuencialmente los productos asociándolos de forma segura a los IDs reales
        foreach ($rawProducts as $index => $item) {
            $secuencial = 1001 + $index;
            
            // Repartir de forma cíclica entre los IDs existentes en la BD
            $brandId = $brandIds[$index % count($brandIds)];
            $categoryId = $categoryIds[$index % count($categoryIds)];

            $compatibilityData = [
                'vehicles' => [
                    ['make' => 'Toyota', 'model' => 'Corolla', 'years' => ['2018', '2020']],
                    ['make' => 'Nissan', 'model' => 'Sentra', 'years' => ['2019']]
                ],
                'note' => 'Validado para ensamblaje original.'
            ];

            Product::create([
                'sku' => "REP-{$secuencial}-A",
                'name' => $item['name'],
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'compatibility' => json_encode($compatibilityData),
                'price' => $item['price'],
                'cost' => $item['cost'],
                'stock' => ($index % 3 == 0) ? 2 : 25,
                'stock_min' => 5,
                'estado' => ($index === 9) ? 0 : 1,
            ]);
        }
    }
}