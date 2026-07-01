<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

/**
 * HU-04: Registro de productos con costo y precio
 *
 * Replica los datos de referencia (SEED) del script SQL:
 * dss_repuestos_automotrices -> tabla brands.
 */
class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Bosch', 'country' => 'Alemania'],
            ['name' => 'NGK', 'country' => 'Japón'],
            ['name' => 'Monroe', 'country' => 'Bélgica'],
            ['name' => 'Ferodo', 'country' => 'Reino Unido'],
            ['name' => 'Gates', 'country' => 'Estados Unidos'],
            ['name' => 'Denso', 'country' => 'Japón'],
            ['name' => 'Mann-Filter', 'country' => 'Alemania'],
            ['name' => 'SKF', 'country' => 'Suecia'],
            ['name' => 'Delphi', 'country' => 'Reino Unido'],
            ['name' => 'AC Delco', 'country' => 'Estados Unidos'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand['name']], $brand);
        }
    }
}
