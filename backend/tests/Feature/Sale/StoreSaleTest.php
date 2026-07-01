<?php

namespace Tests\Feature\Sale;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

/**
 * HU-07: Registro de una venta con múltiples detalles
 */
class StoreSaleTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_registra_una_venta_con_multiples_detalles(): void
    {
        $p1 = Product::factory()->create(['price' => 50, 'cost' => 30, 'stock' => 10]);
        $p2 = Product::factory()->create(['price' => 20, 'cost' => 12, 'stock' => 10]);

        $response = $this->withHeaders($this->authHeader())->postJson('/api/sales', [
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 2],
                ['product_id' => $p2->id, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total_amount', '160.00'); // 2*50 + 3*20

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $p1->id,
            'quantity' => 2,
            'unit_price' => 50,
            'unit_cost' => 30,
            'subtotal' => 100,
        ]);
    }

    public function test_congela_el_precio_aunque_el_producto_cambie_despues(): void
    {
        $product = Product::factory()->create(['price' => 100, 'cost' => 60, 'stock' => 5]);

        $this->withHeaders($this->authHeader())->postJson('/api/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $product->update(['price' => 200, 'cost' => 120]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $product->id,
            'unit_price' => 100,
            'unit_cost' => 60,
        ]);
    }

    public function test_descuenta_el_stock_del_producto(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->withHeaders($this->authHeader())->postJson('/api/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 4]],
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 6]);
    }

    public function test_rechaza_la_venta_si_no_hay_stock_suficiente(): void
    {
        $product = Product::factory()->create(['stock' => 2]);

        $response = $this->withHeaders($this->authHeader())->postJson('/api/sales', [
            'items' => [['product_id' => $product->id, 'quantity' => 5]],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_rechaza_una_venta_sin_items(): void
    {
        $response = $this->withHeaders($this->authHeader())->postJson('/api/sales', [
            'items' => [],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('items');
    }
}
