<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DSSAnalyzer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HU-12: Dashboard interactivo con KPIs generales
 *
 * ⚠️ IMPORTANTE PARA EL MERGE:
 * Este archivo REEMPLAZA el DashboardController placeholder entregado
 * en HU-03 (el que solo devolvía data.user + una nota). La ruta
 * GET /api/dashboard y el middleware ['auth:sanctum','admin'] de
 * routes/api.php NO cambian, así que los tests de HU-03
 * (DashboardAccessTest) siguen pasando sin modificación: 'data.user.role'
 * se sigue devolviendo igual que antes.
 *
 * Criterios de Aceptación HU-12:
 *  - GET /api/dashboard responde con kpis, matrix, top_star,
 *    critical_huso e invoice_summary en un único payload JSON.
 *  - El filtro de fecha por defecto es de los últimos 30 días
 *    (config('dss.default_range_days')) y es ajustable vía
 *    ?start=YYYY-MM-DD&end=YYYY-MM-DD.
 *
 * HU-03: Restricción de acceso al módulo DSS por rol (placeholder original)
 * Este controlador era un PLACEHOLDER para el Sprint 1: su única
 * responsabilidad aquí es demostrar que la ruta /api/dashboard
 * queda correctamente protegida por auth:sanctum + admin.
 * La lógica real de KPIs, matriz Estrella/Hueso y DSSAnalyzer se
 * implementa en HU-10, HU-11 y HU-12 (Sprint posterior), donde este
 * mismo método index() se completará delegando a DSSAnalyzer::buildDashboardData().
 */
class DashboardController extends Controller
{
    public function __construct(private readonly DSSAnalyzer $analyzer) {}

    public function index(Request $request): JsonResponse
    {
        $end = $request->filled('end')
            ? Carbon::parse($request->string('end'))->endOfDay()
            : now();

        $start = $request->filled('start')
            ? Carbon::parse($request->string('start'))->startOfDay()
            : $end->copy()->subDays((int) config('dss.default_range_days'))->startOfDay();

        $data = $this->analyzer->buildDashboardData($start, $end);

        // Placeholder: HU-10/HU-11/HU-12 reemplazarán este bloque
        // con kpis, matrix, top_star, critical_huso e invoice_summary.

        return response()->json([
            'estado' => 'success',
            'message' => 'Dashboard DSS generado correctamente.',
            'data' => array_merge([
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'role' => $request->user()->role,
                ],
            ], $data),
        ], 200);
    }
}
