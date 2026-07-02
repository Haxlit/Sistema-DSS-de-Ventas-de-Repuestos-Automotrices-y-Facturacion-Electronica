<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HU-08: Emisión de factura electrónica (SIN)
 * HU-09: Consolidado de estado de facturación
 *
 * Endpoints:
 *   POST /api/sales/{sale}/issue-invoice
 *   GET  /api/invoices/summary
 */
class InvoiceController extends Controller
{
    /**
     * Emite la factura electrónica asociada a una venta registrada.
     *
     * Criterios de Aceptación HU-08:
     *  - issueInvoice() actualiza invoice_status a 'issued' y genera
     *    invoice_number e invoice_xml_hash (SHA-256).
     *  - Si falla, invoice_status pasa a 'error', nunca queda ambiguo.
     *  - verifyInvoiceHash() permite confirmar la integridad del XML.
     */
    public function issue(Sale $sale): JsonResponse
    {
        $sale->load('details');

        $success = $sale->issueInvoice();

        if (!$success) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo emitir la factura electrónica. La venta quedó marcada como error.',
                'data' => [
                    'sale_id' => $sale->id,
                    'invoice_status' => $sale->invoice_status,
                ],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Factura electrónica emitida correctamente.',
            'data' => [
                'sale_id' => $sale->id,
                'invoice_status' => $sale->invoice_status,
                'invoice_number' => $sale->invoice_number,
                'invoice_xml_hash' => $sale->invoice_xml_hash,
                'invoice_issued_at' => $sale->invoice_issued_at,
            ],
        ], 200);
    }

    /**
     * Consolidado de ventas por estado de facturación en un rango de fechas.
     *
     * Criterios de Aceptación HU-09:
     *  - Agrupa por invoice_status usando el índice idx_sales_invoice_status.
     *  - El rango de fechas por defecto es de los últimos 30 días, igual
     *    que el resto de los KPIs del Dashboard (consistencia con HU-12).
     *
     * Query params:
     *   ?start=YYYY-MM-DD
     *   ?end=YYYY-MM-DD
     */
    public function summary(Request $request): JsonResponse
    {
        $end = $request->filled('end')
            ? \Carbon\Carbon::parse($request->string('end'))->endOfDay()
            : now();

        $start = $request->filled('start')
            ? \Carbon\Carbon::parse($request->string('start'))->startOfDay()
            : $end->copy()->subDays(30)->startOfDay();

        $counts = Sale::enRango($start, $end)
            ->selectRaw('invoice_status, COUNT(*) as total')
            ->groupBy('invoice_status')
            ->pluck('total', 'invoice_status');

        $summary = [
            'issued' => (int) ($counts['issued'] ?? 0),
            'pending' => (int) ($counts['pending'] ?? 0),
            'error' => (int) ($counts['error'] ?? 0),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'range' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
                'invoice_summary' => $summary,
                'total_sales' => array_sum($summary),
            ],
        ], 200);
    }
}
