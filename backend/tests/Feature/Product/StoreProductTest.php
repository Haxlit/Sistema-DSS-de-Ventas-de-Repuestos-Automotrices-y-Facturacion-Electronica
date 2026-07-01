<?php

namespace Tests\Feature\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreProductTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    private function createCategory()
    {
        // Creación manual evitando el Factory ausente
        return ProductCategory::create([
            'name' => 'Repuestos Generales'
        ]);
    }

    public function test_registra_un_producto_con_datos_validos(): void
    {
        $user = User::factory()->create(['role' => 'vendedor']);
        $brand = Brand::factory()->create();
        $category = $this->createCategory(); // <-- Manual

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', [
                'sku' => 'FRN-001',
                'name' => 'Pastillas de Freno',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'price' => 45.00,
                'cost' => 28.50,
                'stock' => 20,
                'stock_min' => 5,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.sku', 'FRN-001')
            ->assertJsonPath('data.contribution_margin', 16.5);

        $this->assertDatabaseHas('products', ['sku' => 'FRN-001']);
    }

    public function test_rechaza_un_sku_duplicado(): void
    {
        $brand = Brand::factory()->create();
        $category = $this->createCategory(); // <-- Manual

        Product::factory()->create([
            'sku' => 'DUP-001', 
            'brand_id' => $brand->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders($this->authHeader())->postJson('/api/products', [
            'sku' => 'DUP-001',
            'name' => 'Otro repuesto',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 10,
            'cost' => 5,
            'stock' => 10,
            'stock_min' => 2,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('sku');
    }

    public function test_rechaza_costo_mayor_al_precio(): void
    {
        $brand = Brand::factory()->create();
        $category = $this->createCategory(); // <-- Manual

        $response = $this->withHeaders($this->authHeader())->postJson('/api/products', [
            'sku' => 'INV-001',
            'name' => 'Repuesto inválido',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 10,
            'cost' => 50,
            'stock' => 10,
            'stock_min' => 2,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('cost');
    }

    public function test_rechaza_la_solicitud_sin_autenticacion(): void
    {
        $brand = Brand::factory()->create();
        $category = $this->createCategory(); // <-- Manual

        $response = $this->postJson('/api/products', [
            'sku' => 'NOAUTH-001',
            'name' => 'Repuesto sin sesión',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 10,
            'cost' => 5,
            'stock' => 10,
            'stock_min' => 2,
        ]);

        $response->assertStatus(401);
    }
}