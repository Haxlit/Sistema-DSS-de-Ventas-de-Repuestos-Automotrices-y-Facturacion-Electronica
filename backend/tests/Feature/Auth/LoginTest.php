<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


/**
 * HU-02: Inicio de sesión con token JWT
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_inicia_sesion_con_credenciales_validas_y_recibe_token(): void
    {
        User::factory()->create([
            'email' => 'vendedor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendedor',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'vendedor@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token', 'token_type', 'user' => ['id', 'name', 'role']],
            ])
            ->assertJsonPath('data.user.role', 'vendedor');
    }

    public function test_rechaza_credenciales_invalidas_sin_revelar_el_campo_incorrecto(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password-incorrecta',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Las credenciales proporcionadas son incorrectas.');
    }

    public function test_rechaza_login_de_un_usuario_con_estado_inactivo(): void
    {
        User::factory()->create([
            'email' => 'inactivo@example.com',
            'password' => Hash::make('password123'),
            'estado' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactivo@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_revoca_el_token_actual(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->tokens);
    }
}
