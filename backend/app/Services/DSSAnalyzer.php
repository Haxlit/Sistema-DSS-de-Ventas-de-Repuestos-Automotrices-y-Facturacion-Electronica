<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * HU-10: Cálculo de tasa de rotación y margen por producto
 *
 * Motor analítico DSS — equivale a la clase "DSSAnalyzer" del Diagrama
 * de Clases (Etapa B) y a la QUERY 3 del Diagrama de Secuencia
 * (agregación de sale_details y sales por producto).
 *
 * ⚠️ NOTA PARA EL MERGE (HU-11 / HU-12):
 * Esta es la primera rebanada del servicio. HU-11 EXTIENDE este mismo
 * archivo agregando classifyProduct() y buildStarHusoMatrix(); HU-12
 * agrega buildDashboardData(). No se debe crear una segunda clase.
 */
class DSSAnalyzer
{
    private float $rotationThreshold;

    private float $marginThreshold;

    private int $rankingSize;

    public function __construct(
        ?float $rotationThreshold = null,
        ?float $marginThreshold = null,
        ?int $rankingSize = null,
    ) {
        $this->rotationThreshold = $rotationThreshold ?? (float) config('dss.rotation_threshold');
        $this->marginThreshold = $marginThreshold ?? (float) config('dss.margin_threshold');
        $this->rankingSize = $rankingSize ?? (int) config('dss.ranking_size');
    }

    /**
     * + computeRotationRate(totalSold, periodDays) : float
     *
     * rotación = unidades_vendidas / días_del_período
     *
     * Criterio de Aceptación HU-10: computeRotationRate(p) = total_sold /
     * período_días, sobre el rango analizado por DSSAnalyzer.
     */
    public function computeRotationRate(int $totalSold, int $periodDays): float
    {
        if ($periodDays <= 0) {
            return 0.0;
        }

        return round($totalSold / $periodDays, 4);
    }

    /**
     * + computeMarginRate(totalRevenue, totalCost) : float
     *
     * margen = (ingresos - costos) / ingresos
     *
     * Criterio de Aceptación HU-10: evita división por cero cuando
     * total_revenue = 0 (producto activo sin ventas en el período).
     */
    public function computeMarginRate(float $totalRevenue, float $totalCost): float
    {
        if ($totalRevenue <= 0.0) {
            return 0.0;
        }

        return round(($totalRevenue - $totalCost) / $totalRevenue, 4);
    }

    /**
     * QUERY 3 (Diagrama de Secuencia): agrega sale_details + sales por
     * producto dentro del rango [start, end] y calcula rotación y
     * margen para cada repuesto ACTIVO del catálogo.
     *
     * Los productos activos sin ventas en el período también se
     * incluyen, con total_sold = 0 y margin_rate = 0, para que el
     * 100% del catálogo quede cubierto (requisito consumido luego por
     * HU-11).
     *
     * @return Collection<int, array{
     *   product_id:int, sku:string, name:string, total_sold:int,
     *   total_revenue:float, total_cost:float, rotation_rate:float,
     *   margin_rate:float
     * }>
     */
    public function getProductStats(Carbon $start, Carbon $end): Collection
    {
        $periodDays = max(1, $start->copy()->startOfDay()->diffInDays($end->copy()->endOfDay()) + 1);

        $aggregates = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->selectRaw('
                sale_details.product_id as product_id,
                SUM(sale_details.quantity) as total_sold,
                SUM(sale_details.subtotal) as total_revenue,
                SUM(sale_details.quantity * sale_details.unit_cost) as total_cost
            ')
            ->groupBy('sale_details.product_id')
            ->get()
            ->keyBy('product_id');

        return Product::activos()
            ->get(['id', 'sku', 'name'])
            ->map(function (Product $product) use ($aggregates, $periodDays) {
                $row = $aggregates->get($product->id);

                $totalSold = (int) ($row->total_sold ?? 0);
                $totalRevenue = (float) ($row->total_revenue ?? 0);
                $totalCost = (float) ($row->total_cost ?? 0);

                return [
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'total_sold' => $totalSold,
                    'total_revenue' => round($totalRevenue, 2),
                    'total_cost' => round($totalCost, 2),
                    'rotation_rate' => $this->computeRotationRate($totalSold, $periodDays),
                    'margin_rate' => $this->computeMarginRate($totalRevenue, $totalCost),
                ];
            });
    }

    public function getRotationThreshold(): float
    {
        return $this->rotationThreshold;
    }

    public function getMarginThreshold(): float
    {
        return $this->marginThreshold;
    }

    public function getRankingSize(): int
    {
        return $this->rankingSize;
    }
}
