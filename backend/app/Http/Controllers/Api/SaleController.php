<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * HU-07: Registro de una venta con múltiples detalles
 *
 * Endpoint: POST /api/sales
 *
 * NOTA: el método issueInvoice (HU-08) y getInvoiceSummary (HU-09) se
 * agregan en sus respectivas historias, en un controlador separado o
 * como métodos adicionales aquí, según se acuerde con esas historias;
 * por ahora este controlador SOLO contiene store().
 */
class SaleController extends Controller
{
    /**
     * Registra una venta compuesta de uno o más repuestos.
     *
     * Criterios de Aceptación HU-07:
     *  - Cada SaleDetail congela quantity, unit_price y unit_cost al
     *    momento exacto de la transacción (se copian de Product en
     *    este instante, no se referencian dinámicamente).
     *  - subtotal = quantity * unit_price, calculado en el servidor.
     *  - Sale.total_amount = suma de los subtotal de sus SaleDetail.
     *  - Una venta sin al menos un SaleDetail es rechazada (lo valida
     *    StoreSaleRequest antes de llegar aquí).
     *  - Se descuenta el stock del producto y se rechaza la venta si
     *    no hay stock suficiente (regla de negocio adicional, fiel a
     *    los triggers del SQL de referencia que descuentan stock
     *    automáticamente al insertar en sale_details).
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $items = $request->validated()['items'];

        $sale = DB::transaction(function () use ($items, $request) {
            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'invoice_status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                // lockForUpdate evita condiciones de carrera de stock
                // si dos vendedores registran ventas simultáneas.
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $product->estado) {
                    throw ValidationException::withMessages([
                        'items' => "El repuesto '{$product->name}' está dado de baja y no puede venderse.",
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para '{$product->name}'. Disponible: {$product->stock}.",
                    ]);
                }

                $unitPrice = (float) $product->price;
                $unitCost = (float) $product->cost;
                $subtotal = round($unitPrice * $item['quantity'], 2);

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);

                $totalAmount += $subtotal;
            }

            $sale->update(['total_amount' => round($totalAmount, 2)]);

            return $sale;
        });

        $sale->load('details.product');

        return response()->json([
            'status' => 'success',
            'message' => 'Venta registrada correctamente.',
            'data' => [
                'id' => $sale->id,
                'total_amount' => $sale->total_amount,
                'invoice_status' => $sale->invoice_status,
                'items' => $sale->details->map(fn($d) => [
                    'product' => $d->product->name,
                    'quantity' => $d->quantity,
                    'unit_price' => $d->unit_price,
                    'subtotal' => $d->subtotal,
                ]),
            ],
        ], 201);
    }
}
