<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * HU-01: Registro y autenticación de usuarios
 *
 * Endpoint: POST /api/register
 *
 * NOTA: el método login() de este mismo flujo de autenticación
 * se implementa en HU-02 (ver AuthController en esa rama / Issue).
 * Este controlador se deja abierto para que la Persona 2 agregue
 * el método login() sin generar conflictos de merge en otros archivos.
 */
class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario del negocio (admin o vendedor).
     *
     * Criterios de Aceptación HU-01:
     *  - email único, password con hash bcrypt.
     *  - role por defecto 'vendedor' si no se especifica.
     *  - nunca se devuelve ni se almacena la contraseña en texto plano.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'vendedor',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario registrado correctamente.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }
}
