<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-05: Edición y baja de productos del catálogo
 */
class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_actualiza_el_precio_de_un_producto(): void
    {
        $product = Product::factory()->create(['price' => 50, 'cost' => 30]);

        $response = $this->withHeaders($this->authHeader())
            ->putJson("/api/products/{$product->id}", ['price' => 60]);

        $response->assertStatus(200)->assertJsonPath('data.price', '60.00');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 60]);
    }

    public function test_valida_sku_unico_excluyendo_el_propio_registro(): void
    {
        $productA = Product::factory()->create(['sku' => 'AAA-001']);
        $productB = Product::factory()->create(['sku' => 'BBB-001']);

        // Actualizar productA con su propio sku debe ser válido.
        $response = $this->withHeaders($this->authHeader())
            ->putJson("/api/products/{$productA->id}", ['sku' => 'AAA-001']);
        $response->assertStatus(200);

        // Intentar usar el sku de productB en productA debe fallar.
        $response = $this->withHeaders($this->authHeader())
            ->putJson("/api/products/{$productA->id}", ['sku' => 'BBB-001']);
        $response->assertStatus(422)->assertJsonValidationErrors('sku');
    }

    public function test_da_de_baja_un_producto_sin_eliminarlo(): void
    {
        $product = Product::factory()->create(['estado' => true]);

        $response = $this->withHeaders($this->authHeader())
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'estado' => false]);
    }
}
