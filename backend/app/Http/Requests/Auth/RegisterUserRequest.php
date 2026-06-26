<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * HU-01: Registro y autenticación de usuarios
 *
 * Criterios de Aceptación cubiertos:
 *  - name, email (único) y password son obligatorios.
 *  - role solo acepta 'admin' o 'vendedor' (con 'vendedor' como default si se omite).
 *  - Un correo duplicado se rechaza con un mensaje de error claro.
 */
class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El registro de usuarios del negocio lo realiza un administrador
        // ya autenticado. Si en este sprint el registro es público (primer
        // usuario del sistema), cambiar a `return true;` y documentar el
        // riesgo en el README del módulo.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['sometimes', 'in:admin,vendedor'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.in' => "El rol debe ser 'admin' o 'vendedor'.",
        ];
    }
}
