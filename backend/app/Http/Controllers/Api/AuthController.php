<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * HU-01: Registro de Usuarios
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,vendedor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'vendedor', // Rol por defecto si no se envía
        ]);

        // Generamos el token de acceso (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * HU-02: Inicio de Sesión (Login)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buscamos el usuario asegurando que esté ACTIVO usando el scope de tu modelo
        $user = User::activos()->where('email', $request->email)->first();

        // Validamos credenciales y existencia del usuario
        if (!$user || !Hash::make($request->password, ['fallback' => $user->password])) {
            // Nota técnica: Laravel 11 maneja Hash::check automáticamente si pasas texto plano,
            // pero si usas el método nativo es: Hash::check($request->password, $user->password)
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Credenciales invalidas o cuenta inactiva.'
                ], 401);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->getFullName(),
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => $user->isAdmin(),
                'access_dss' => $user->hasAccessToDSS() // HU-03: Control de acceso al Dashboard
            ]
        ], 200);
    }

    /**
     * Cierre de Sesión (Logout)
     */
    public function logout(Request $request)
    {
        // Revoca el token con el que el usuario está autenticado actualmente
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }
}