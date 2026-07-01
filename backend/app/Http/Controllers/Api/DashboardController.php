<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HU-03: Restricción de acceso al módulo DSS por rol
 *
 * Este controlador es un PLACEHOLDER para el Sprint 1: su única
 * responsabilidad aquí es demostrar que la ruta /api/dashboard
 * queda correctamente protegida por auth:sanctum + admin.
 *
 * La lógica real de KPIs, matriz Estrella/Hueso y DSSAnalyzer se
 * implementa en HU-10, HU-11 y HU-12 (Sprint posterior), donde este
 * mismo método index() se completará delegando a DSSAnalyzer::buildDashboardData().
 */
class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'estado' => 'success',
            'message' => 'Acceso al módulo DSS confirmado.',
            'data' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'role' => $request->user()->role,
                ],
                // Placeholder: HU-10/HU-11/HU-12 reemplazarán este bloque
                // con kpis, matrix, top_star, critical_huso e invoice_summary.
                'note' => 'Endpoint protegido y listo. Los datos analíticos del DSS se implementan en el siguiente sprint.',
            ],
        ], 200);
    }
}
