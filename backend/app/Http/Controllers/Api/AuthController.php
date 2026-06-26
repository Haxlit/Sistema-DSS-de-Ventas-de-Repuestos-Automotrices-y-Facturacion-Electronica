<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * HU-02: Inicio de sesión con token JWT
 *
 * IMPORTANTE PARA EL MERGE:
 * Este archivo AGREGA los métodos login() y logout() al AuthController
 * que la Persona 1 ya creó en HU-01 (con el método register()).
 * No reemplazar el archivo completo: copiar únicamente los métodos
 * login() y logout() dentro de la clase AuthController existente.
 *
 * Endpoints:
 *   POST /api/auth/login
 *   POST /api/auth/logout  (requiere middleware auth:sanctum)
 */
class AuthController extends Controller
{
    /**
     * Inicia sesión y devuelve un token de acceso (Laravel Sanctum).
     *
     * Criterios de Aceptación HU-02:
     *  - Credenciales válidas -> token + datos básicos del usuario (id, name, role).
     *  - Credenciales inválidas -> HTTP 401 sin revelar cuál campo fue incorrecto.
     *  - El token se envía como Bearer y se gestiona vía Sanctum (expiración
     *    configurable en config/sanctum.php -> 'expiration').
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::activos()->where('email', $credentials['email'])->first();

        if (! $user || ! Auth::validate($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        // Revoca tokens previos del mismo dispositivo lógico (opcional, simplifica pruebas)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
            ],
        ], 200);
    }

    /**
     * Cierra la sesión revocando el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }
}
