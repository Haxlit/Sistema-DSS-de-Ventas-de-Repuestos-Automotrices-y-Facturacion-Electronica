<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HU-03: Restricción de acceso al módulo DSS por rol
 *
 * Equivale al "AuthMiddleware" del Diagrama de Secuencia (Etapa B,
 * Sección 4.2): valida que el usuario autenticado tenga rol 'admin'
 * (assertRole(user, 'admin')) antes de delegar al DashboardController.
 *
 * Se aplica DESPUÉS de auth:sanctum en la definición de rutas, ya que
 * depende de que $request->user() exista.
 *
 * Criterios de Aceptación:
 *  - Token inválido o expirado -> ya lo maneja auth:sanctum (401).
 *  - Token válido pero rol != 'admin' -> HTTP 403 Forbidden.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasAccessToDSS()) {
            return response()->json([
                'estado' => 'error',
                'message' => 'No tiene permisos para acceder al módulo DSS.',
            ], 403);
        }

        return $next($request);
    }
}
