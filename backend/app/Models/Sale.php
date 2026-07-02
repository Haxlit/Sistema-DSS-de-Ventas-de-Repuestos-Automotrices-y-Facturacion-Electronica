<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HU-08: Emisión de factura electrónica (SIN)
 * HU-09: Consolidado de estado de facturación
 *
 * ⚠️ IMPORTANTE PARA EL MERGE:
 * Este archivo REEMPLAZA el Sale.php entregado en HU-06/HU-07, que
 * tenía issueInvoice() y verifyInvoiceHash() como placeholders que
 * lanzaban una excepción. Aquí ya están implementados de verdad.
 * El resto de los métodos (getDetails, getTotalCost, getTotalMargin)
 * se mantienen exactamente iguales.
 */
class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'invoice_status',
        'invoice_number',
        'invoice_xml_hash',
        'invoice_issued_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'invoice_issued_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getTotalCost(): float
    {
        return round(
            $this->details->sum(fn(SaleDetail $d) => $d->quantity * (float) $d->unit_cost),
            2
        );
    }

    public function getTotalMargin(): float
    {
        return round((float) $this->total_amount - $this->getTotalCost(), 2);
    }

    /**
     * + issueInvoice() : boolean
     *
     * Criterios de Aceptación HU-08:
     *  - Genera invoice_number e invoice_xml_hash (SHA-256) y actualiza
     *    invoice_status a 'issued'.
     *  - Si el servicio de facturación falla, invoice_status pasa a
     *    'error' y la venta no queda en estado ambiguo.
     *  - El estado por defecto de toda venta nueva es 'pending'
     *    (ya garantizado por la migración de sales en HU-07).
     *
     * NOTA: el cuerpo real de comunicación con el SIN (Servicio de
     * Impuestos Nacionales) se trata como un servicio externo / mock
     * en este sprint, según el alcance MVP ("No envía ni valida
     * comprobantes directamente ante la entidad fiscal", Etapa A,
     * Cuadro Es/No es). Aquí se simula la emisión generando el hash
     * y el número de factura de forma determinística y verificable.
     */
    public function issueInvoice(): bool
    {
        if ($this->invoice_status === 'issued') {
            return true; // idempotente: ya estaba emitida.
        }

        try {
            $payload = $this->buildInvoicePayload();
            $hash = hash('sha256', $payload);
            $number = $this->generateInvoiceNumber();

            $this->update([
                'invoice_status' => 'issued',
                'invoice_number' => $number,
                'invoice_xml_hash' => $hash,
                'invoice_issued_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->update(['invoice_status' => 'error']);

            report($e);

            return false;
        }
    }

    /**
     * + verifyInvoiceHash() : boolean
     * Confirma que el hash almacenado corresponde al payload actual
     * de la venta (detecta manipulación posterior a la emisión).
     */
    public function verifyInvoiceHash(): bool
    {
        if (!$this->invoice_xml_hash) {
            return false;
        }

        $payload = $this->buildInvoicePayload();

        return hash_equals($this->invoice_xml_hash, hash('sha256', $payload));
    }

    /**
     * Construye el payload determinístico que se firma con SHA-256.
     * Incluye id, total y las líneas de detalle para que cualquier
     * alteración posterior invalide el hash.
     */
    private function buildInvoicePayload(): string
    {
        $lines = $this->details->map(
            fn(SaleDetail $d) => "{$d->product_id}:{$d->quantity}:{$d->unit_price}"
        )->implode('|');

        return "{$this->id}:{$this->user_id}:{$this->total_amount}:{$lines}";
    }

    private function generateInvoiceNumber(): string
    {
        return sprintf('FAC-%s-%06d', now()->format('Ymd'), $this->id);
    }

    public function scopeEnRango($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
