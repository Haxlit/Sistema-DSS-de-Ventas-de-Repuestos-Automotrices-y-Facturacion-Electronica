<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HU-01: Registro y autenticación de usuarios
 *
 * Cada test mapea directamente a un criterio de aceptación
 * de la Definition of Ready de la historia.
 */
class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_un_usuario_con_rol_vendedor_por_defecto(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'vendedor');

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
            'role' => 'vendedor',
        ]);
    }

    public function test_registra_un_usuario_con_rol_admin_explicito(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Ana Gómez',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'admin');
    }

    public function test_rechaza_un_correo_duplicado(): void
    {
        User::factory()->create(['email' => 'duplicado@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Otro Usuario',
            'email' => 'duplicado@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_rechaza_un_rol_invalido(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Usuario Inválido',
            'email' => 'invalido@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'superadmin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('role');
    }

    public function test_la_contrasena_nunca_se_devuelve_en_la_respuesta(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Usuario Seguro',
            'email' => 'seguro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertJsonMissingPath('data.password');
    }

    public function test_la_contrasena_se_almacena_con_hash_bcrypt(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Usuario Hash',
            'email' => 'hash@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'hash@example.com')->first();

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(str_starts_with($user->password, '$2y$'));
    }
}
