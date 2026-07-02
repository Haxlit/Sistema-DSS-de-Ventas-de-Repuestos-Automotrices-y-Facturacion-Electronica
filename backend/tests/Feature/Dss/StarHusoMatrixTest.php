<?php

namespace Tests\Feature\Dss;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\DSSAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-11: Clasificación matricial Estrella / Hueso
 */
class StarHusoMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function analyzer(): DSSAnalyzer
    {
        return new DSSAnalyzer(rotationThreshold: 0.5, marginThreshold: 0.20, rankingSize: 5);
    }

    /**
     * Crea una venta con un detalle que produce exactamente la
     * rotación y el margen deseados dentro de un período de 10 días.
     */
    private function crearProductoConDesempeno(int $totalSold, float $unitPrice, float $unitCost): Product
    {
        $product = Product::factory()->create(['estado' => true]);

        $sale = Sale::factory()->create(['created_at' => now()->subDays(2)]);

        SaleDetail::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $totalSold,
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
            'subtotal' => $unitPrice * $totalSold,
        ]);

        return $product;
    }

    public function test_clasifica_el_100_por_ciento_del_catalogo_activo(): void
    {
        Product::factory()->count(3)->create(['estado' => true]);
        Product::factory()->count(2)->create(['estado' => false]); // no deben contarse

        $matrix = $this->analyzer()->buildStarHusoMatrix(now()->subDays(9)->startOfDay(), now()->endOfDay());

        $this->assertSame(3, $matrix['total_classified']);
    }

    public function test_un_producto_de_alta_rotacion_y_alto_margen_cae_en_top_star(): void
    {
        // 10 días de período, rotación = 10 unidades / 10 días = 1.0 (≥ 0.5)
        // margen = (100 - 40) / 100 = 0.6 (≥ 0.20)
        $estrella = $this->crearProductoConDesempeno(totalSold: 10, unitPrice: 10, unitCost: 4);

        $matrix = $this->analyzer()->buildStarHusoMatrix(now()->subDays(9)->startOfDay(), now()->endOfDay());

        $ids = collect($matrix['top_star'])->pluck('product_id');
        $this->assertTrue($ids->contains($estrella->id));
        $this->assertLessThanOrEqual(5, count($matrix['top_star']));
    }

    public function test_un_producto_de_baja_rotacion_y_bajo_margen_cae_en_critical_huso(): void
    {
        // rotación = 1 / 10 = 0.1 (< 0.5), margen = (10 - 9.5) / 10 = 0.05 (< 0.20)
        $hueso = $this->crearProductoConDesempeno(totalSold: 1, unitPrice: 10, unitCost: 9.5);

        $matrix = $this->analyzer()->buildStarHusoMatrix(now()->subDays(9)->startOfDay(), now()->endOfDay());

        $ids = collect($matrix['critical_huso'])->pluck('product_id');
        $this->assertTrue($ids->contains($hueso->id));
        $this->assertLessThanOrEqual(5, count($matrix['critical_huso']));
    }

    public function test_top_star_no_excede_el_tamano_de_ranking_configurado(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->crearProductoConDesempeno(totalSold: 10, unitPrice: 10, unitCost: 3);
        }

        $matrix = $this->analyzer()->buildStarHusoMatrix(now()->subDays(9)->startOfDay(), now()->endOfDay());

        $this->assertCount(5, $matrix['top_star']);
    }

    public function test_los_cuatro_cuadrantes_siempre_estan_presentes_en_la_respuesta(): void
    {
        $matrix = $this->analyzer()->buildStarHusoMatrix(now()->subDays(9)->startOfDay(), now()->endOfDay());

        $this->assertArrayHasKey(DSSAnalyzer::ESTRELLA, $matrix['quadrants']);
        $this->assertArrayHasKey(DSSAnalyzer::VACA, $matrix['quadrants']);
        $this->assertArrayHasKey(DSSAnalyzer::INTERROGANTE, $matrix['quadrants']);
        $this->assertArrayHasKey(DSSAnalyzer::HUESO, $matrix['quadrants']);
    }
}
