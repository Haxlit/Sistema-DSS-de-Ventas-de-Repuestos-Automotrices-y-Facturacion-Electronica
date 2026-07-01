<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

/**
 * HU-07: Registro de una venta con múltiples detalles
 *
 * Criterios de Aceptación cubiertos:
 *  - Una venta debe componerse de al menos un detalle (items array,
 *    composición fuerte 1..* del diagrama de clases).
 *  - Cada línea exige product_id válido y quantity > 0.
 */
class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'La venta debe incluir al menos un repuesto.',
            'items.min' => 'La venta debe incluir al menos un repuesto.',
            'items.*.product_id.exists' => 'Uno de los repuestos seleccionados no existe en el catálogo.',
            'items.*.quantity.min' => 'La cantidad de cada línea debe ser mayor a 0.',
        ];
    }
}
