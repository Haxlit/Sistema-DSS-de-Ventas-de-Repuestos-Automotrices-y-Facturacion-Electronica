<?php

namespace Tests\Feature\Product;

use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-06: Búsqueda y filtrado de productos
 */
class SearchProductTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_busca_un_producto_por_sku_exacto(): void
    {
        Product::factory()->create(['sku' => 'EXACT-001', 'name' => 'Filtro de aceite']);
        Product::factory()->create(['sku' => 'OTHER-002', 'name' => 'Bujía de encendido']);

        $response = $this->withHeaders($this->authHeader())
            ->getJson('/api/products?q=EXACT-001');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('EXACT-001', $response->json('data.0.sku'));
    }

    public function test_filtra_productos_por_marca(): void
    {
        $bosch = Brand::factory()->create(['name' => 'Bosch']);
        $ngk = Brand::factory()->create(['name' => 'NGK']);

        Product::factory()->count(2)->create(['brand_id' => $bosch->id]);
        Product::factory()->create(['brand_id' => $ngk->id]);

        $response = $this->withHeaders($this->authHeader())
            ->getJson("/api/products?brand_id={$bosch->id}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_la_respuesta_incluye_stock_disponible(): void
    {
        Product::factory()->create(['stock' => 15]);

        $response = $this->withHeaders($this->authHeader())->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.stock', 15);
    }

    public function test_excluye_productos_dados_de_baja_por_defecto(): void
    {
        Product::factory()->create(['estado' => true]);
        Product::factory()->create(['estado' => false]);

        $response = $this->withHeaders($this->authHeader())->getJson('/api/products');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
