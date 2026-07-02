<?php

namespace Tests\Feature\Invoice;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-09: Consolidado de estado de facturación
 */
class InvoiceSummaryTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_agrupa_las_ventas_por_estado_de_factura(): void
    {
        Sale::factory()->count(3)->create(['invoice_status' => 'issued']);
        Sale::factory()->count(2)->create(['invoice_status' => 'pending']);
        Sale::factory()->count(1)->create(['invoice_status' => 'error']);

        $response = $this->withHeaders($this->authHeader())
            ->getJson('/api/invoices/summary');

        $response->assertStatus(200)
            ->assertJsonPath('data.invoice_summary.issued', 3)
            ->assertJsonPath('data.invoice_summary.pending', 2)
            ->assertJsonPath('data.invoice_summary.error', 1)
            ->assertJsonPath('data.total_sales', 6);
    }

    public function test_usa_los_ultimos_30_dias_por_defecto(): void
    {
        Sale::factory()->create([
            'invoice_status' => 'issued',
            'created_at' => now()->subDays(5),
        ]);
        Sale::factory()->create([
            'invoice_status' => 'issued',
            'created_at' => now()->subDays(45), // fuera de rango
        ]);

        $response = $this->withHeaders($this->authHeader())
            ->getJson('/api/invoices/summary');

        $response->assertStatus(200)
            ->assertJsonPath('data.invoice_summary.issued', 1);
    }

    public function test_acepta_un_rango_de_fechas_personalizado(): void
    {
        Sale::factory()->create([
            'invoice_status' => 'pending',
            'created_at' => '2025-01-15',
        ]);

        $response = $this->withHeaders($this->authHeader())
            ->getJson('/api/invoices/summary?start=2025-01-01&end=2025-01-31');

        $response->assertStatus(200)
            ->assertJsonPath('data.invoice_summary.pending', 1);
    }
}
