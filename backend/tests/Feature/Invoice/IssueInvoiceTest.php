<?php

namespace Tests\Feature\Invoice;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-08: Emisión de factura electrónica (SIN)
 */
class IssueInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_emite_la_factura_y_genera_numero_y_hash(): void
    {
        $sale = Sale::factory()->create(['invoice_status' => 'pending', 'total_amount' => 100]);
        SaleDetail::factory()->create(['sale_id' => $sale->id]);

        $response = $this->withHeaders($this->authHeader())
            ->postJson("/api/sales/{$sale->id}/issue-invoice");

        $response->assertStatus(200)
            ->assertJsonPath('data.invoice_status', 'issued');

        $sale->refresh();
        $this->assertNotNull($sale->invoice_number);
        $this->assertEquals(64, strlen($sale->invoice_xml_hash));
    }

    public function test_el_estado_por_defecto_de_una_venta_nueva_es_pending(): void
    {
        $sale = Sale::factory()->create();

        $this->assertEquals('pending', $sale->invoice_status);
    }

    public function test_es_idempotente_si_la_factura_ya_fue_emitida(): void
    {
        $sale = Sale::factory()->create(['invoice_status' => 'pending']);
        SaleDetail::factory()->create(['sale_id' => $sale->id]);

        $sale->load('details');
        $sale->issueInvoice();
        $firstHash = $sale->invoice_xml_hash;

        $result = $sale->issueInvoice();

        $this->assertTrue($result);
        $this->assertEquals($firstHash, $sale->invoice_xml_hash);
    }

    public function test_verify_invoice_hash_detecta_manipulacion(): void
    {
        $sale = Sale::factory()->create(['invoice_status' => 'pending']);
        SaleDetail::factory()->create(['sale_id' => $sale->id]);
        $sale->load('details');
        $sale->issueInvoice();

        $this->assertTrue($sale->verifyInvoiceHash());

        $sale->update(['total_amount' => 99999]);
        $sale->refresh()->load('details');

        $this->assertFalse($sale->verifyInvoiceHash());
    }
}
