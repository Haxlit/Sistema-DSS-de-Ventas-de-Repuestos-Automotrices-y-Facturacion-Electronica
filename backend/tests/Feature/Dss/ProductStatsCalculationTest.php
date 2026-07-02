<?php

namespace Tests\Feature\Dss;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\DSSAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-10: Cálculo de tasa de rotación y margen por producto
 *
 * Verifica QUERY 3: la agregación real de sale_details + sales por
 * producto (getProductStats), sobre una base de datos de pruebas.
 */
class ProductStatsCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agrega_ventas_de_un_producto_dentro_del_rango(): void
    {
        $product = Product::factory()->create(['estado' => true]);

        $sale = Sale::factory()->create(['created_at' => now()->subDays(5)]);
        SaleDetail::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 50,
            'unit_cost' => 30,
            'subtotal' => 500,
        ]);

        $analyzer = new DSSAnalyzer(0.5, 0.20);
        $stats = $analyzer->getProductStats(now()->subDays(30), now());
        $row = $stats->firstWhere('product_id', $product->id);

        $this->assertNotNull($row);
        $this->assertSame(10, $row['total_sold']);
        $this->assertSame(500.0, $row['total_revenue']);
        $this->assertSame(300.0, $row['total_cost']);
        // margen = (500 - 300) / 500 = 0.4
        $this->assertSame(0.4, $row['margin_rate']);
    }

    public function test_ignora_ventas_fuera_del_rango_de_fechas(): void
    {
        $product = Product::factory()->create(['estado' => true]);

        $saleFuera = Sale::factory()->create(['created_at' => now()->subDays(90)]);

        // Ejecutamos sin eventos para que ningún Observer altere el subtotal consistente
        SaleDetail::withoutEvents(function () use ($saleFuera, $product) {
            SaleDetail::factory()->create([
                'sale_id' => $saleFuera->id,
                'product_id' => $product->id,
                'quantity' => 20,
                'unit_price' => 50.00,
                'unit_cost' => 30.00,
                'subtotal' => 1000.00, // 20 * 50 = 1000 EXACTOS
            ]);
        });

        $analyzer = new DSSAnalyzer();
        $stats = $analyzer->getProductStats(now()->subDays(30), now());
        $row = $stats->firstWhere('product_id', $product->id);

        $this->assertSame(0, $row['total_sold']);
        $this->assertSame(0.0, $row['margin_rate']);
    }

    public function test_incluye_productos_activos_sin_ventas_en_el_periodo(): void
    {
        $sinVentas = Product::factory()->create(['estado' => true]);

        $analyzer = new DSSAnalyzer();
        $stats = $analyzer->getProductStats(now()->subDays(30), now());

        $row = $stats->firstWhere('product_id', $sinVentas->id);

        $this->assertNotNull($row, 'El producto activo debe aparecer aunque no tenga ventas.');
        $this->assertSame(0, $row['total_sold']);
        $this->assertSame(0.0, $row['rotation_rate']);
        $this->assertSame(0.0, $row['margin_rate']);
    }

    public function test_no_incluye_productos_dados_de_baja(): void
    {
        $inactivo = Product::factory()->create(['estado' => false]);

        $analyzer = new DSSAnalyzer();
        $stats = $analyzer->getProductStats(now()->subDays(30), now());

        $this->assertNull($stats->firstWhere('product_id', $inactivo->id));
    }

    public function test_la_rotacion_usa_los_dias_del_periodo_analizado(): void
    {
        $product = Product::factory()->create(['estado' => true]);
        $sale = Sale::factory()->create(['created_at' => now()->subDays(2)]);

        // Ejecutamos sin eventos para asegurar la consistencia matemática en MariaDB
        SaleDetail::withoutEvents(function () use ($sale, $product) {
            SaleDetail::factory()->create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => 10,
                'unit_price' => 50.00,
                'unit_cost' => 30.00,
                'subtotal' => 500.00, // 10 * 50 = 500 EXACTOS
            ]);
        });

        // Rango de exactamente 10 días => rotación = 10 / 10 = 1.0
        $analyzer = new DSSAnalyzer();
        $stats = $analyzer->getProductStats(now()->subDays(9)->startOfDay(), now()->endOfDay());
        $row = $stats->firstWhere('product_id', $product->id);

        $this->assertSame(1.0, $row['rotation_rate']);
    }
}
