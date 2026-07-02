<?php

namespace Tests\Feature\Dashboard;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-12: Dashboard interactivo con KPIs generales
 *
 * Complementa DashboardAccessTest (HU-03, que solo prueba control de
 * acceso) verificando el CONTENIDO del payload: kpis, matrix, top_star,
 * critical_huso e invoice_summary.
 */
class DashboardKpiTest extends TestCase
{
    use RefreshDatabase;

    private function adminHeader(): array
    {
        $admin = User::factory()->create(['role' => 'admin', 'estado' => 1]);
        $token = $admin->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_el_payload_incluye_las_cinco_secciones_requeridas(): void
    {
        $response = $this->withHeaders($this->adminHeader())->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'range' => ['start', 'end'],
                    'kpis' => ['total_revenue', 'total_sales', 'active_products'],
                    'matrix' => ['ESTRELLA', 'VACA', 'INTERROGANTE', 'HUESO'],
                    'top_star',
                    'critical_huso',
                    'invoice_summary' => ['issued', 'pending', 'error'],
                ],
            ]);
    }

    public function test_los_kpis_reflejan_las_ventas_del_periodo(): void
    {
        Product::factory()->count(2)->create(['estado' => true]);

        $sale = Sale::factory()->create([
            'total_amount' => 250,
            'created_at' => now()->subDays(3),
        ]);
        SaleDetail::factory()->create(['sale_id' => $sale->id]);

        $response = $this->withHeaders($this->adminHeader())->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('data.kpis.total_sales', 1);

        $this->assertEqualsWithDelta(250.0, (float) $response->json('data.kpis.total_revenue'), 0.001);
    }

    public function test_active_products_solo_cuenta_productos_con_estado_activo(): void
    {
        Product::factory()->count(3)->create(['estado' => true]);
        Product::factory()->count(2)->create(['estado' => false]);

        $response = $this->withHeaders($this->adminHeader())->getJson('/api/dashboard');

        $response->assertJsonPath('data.kpis.active_products', 3);
    }

    public function test_el_invoice_summary_coincide_con_el_de_hu09(): void
    {
        Sale::factory()->count(2)->create(['invoice_status' => 'issued']);
        Sale::factory()->count(1)->create(['invoice_status' => 'pending']);

        $response = $this->withHeaders($this->adminHeader())->getJson('/api/dashboard');

        $response->assertJsonPath('data.invoice_summary.issued', 2)
            ->assertJsonPath('data.invoice_summary.pending', 1)
            ->assertJsonPath('data.invoice_summary.error', 0);
    }

    public function test_el_rango_por_defecto_es_de_treinta_dias(): void
    {
        $response = $this->withHeaders($this->adminHeader())->getJson('/api/dashboard');

        $start = $response->json('data.range.start');
        $end = $response->json('data.range.end');

        $this->assertEqualsWithDelta(
            30,
            \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end)),
            1
        );
    }

    public function test_acepta_un_rango_de_fechas_personalizado(): void
    {
        $sale = Sale::factory()->create([
            'total_amount' => 99,
            'created_at' => '2025-02-10',
        ]);
        SaleDetail::factory()->create(['sale_id' => $sale->id]);

        $response = $this->withHeaders($this->adminHeader())
            ->getJson('/api/dashboard?start=2025-02-01&end=2025-02-28');

        $response->assertStatus(200)
            ->assertJsonPath('data.kpis.total_sales', 1)
            ->assertJsonPath('data.range.start', '2025-02-01')
            ->assertJsonPath('data.range.end', '2025-02-28');
    }

    public function test_un_vendedor_sigue_sin_poder_acceder(): void
    {
        $vendedor = User::factory()->create(['role' => 'vendedor']);
        $token = $vendedor->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dashboard');

        $response->assertStatus(403);
    }
}
