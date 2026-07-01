<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-03: Restricción de acceso al módulo DSS por rol
 */
class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_autenticado_accede_al_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'estado' => 1
        ]);

        $token = $admin->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.role', 'admin');
    }

    public function test_un_vendedor_recibe_403_al_intentar_acceder_al_dashboard(): void
    {
        $vendedor = User::factory()->create(['role' => 'vendedor']);
        $token = $vendedor->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dashboard');

        $response->assertStatus(403);
    }

    public function test_una_solicitud_sin_token_recibe_401(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401);
    }

    public function test_un_admin_con_estado_inactivo_no_tiene_acceso(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'estado' => false]);
        $token = $admin->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/dashboard');

        $response->assertStatus(403);
    }
}
