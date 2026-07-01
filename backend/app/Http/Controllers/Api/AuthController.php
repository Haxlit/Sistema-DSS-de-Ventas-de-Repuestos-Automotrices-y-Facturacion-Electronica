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
            'password' => $request->password, // El cast 'hashed' en el modelo User se encarga del Hash automáticamente
            'role' => $request->role ?? 'vendedor',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Estructura envuelta en 'data' para cumplir con RegisterUserTest
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
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

        // Buscamos el usuario por su email
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe o la contraseña no coincide
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.' // Mensaje exacto esperado por el test
            ], 401);
        }

        // Regla de negocio: Impedir login si el usuario está inactivo (estado = false)
        if ($user->estado === false) {
            return response()->json([
                'message' => 'Cuenta inactiva.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Estructura unificada bajo 'data' tal como lo valida LoginTest.php:34
        return response()->json([
            'message' => 'Login correcto',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'role' => $user->role,
                    'email' => $user->email,
                    'is_admin' => $user->isAdmin(),
                    'access_dss' => $user->hasAccessToDSS()
                ]
            ]
        ], 200);
    }

    /**
     * Cierre de Sesión (Logout)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }
}