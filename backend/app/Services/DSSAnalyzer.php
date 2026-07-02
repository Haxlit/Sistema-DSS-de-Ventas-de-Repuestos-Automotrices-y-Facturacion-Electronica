<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * HU-10: Cálculo de tasa de rotación y margen por producto
 * HU-11: Clasificación matricial Estrella / Hueso
 * HU-12: Dashboard interactivo con KPIs generales
 *
 * Motor analítico DSS — equivale a la clase "DSSAnalyzer" del Diagrama
 * de Clases (Etapa B) y a la QUERY 3 del Diagrama de Secuencia
 * (agregación de sale_details y sales por producto).
 *
 * ⚠️ IMPORTANTE PARA EL MERGE:
 * Este archivo REEMPLAZA el DSSAnalyzer.php de HU-11. Todo lo de HU-10
 * y HU-11 se mantiene EXACTAMENTE igual; lo único nuevo es la sección
 * "HU-12" (buildDashboardData). Esta es la versión final del servicio
 * para el Sprint de Motor DSS / Dashboard.
 *
 * Cuadrantes de clasificación (HU-11):
 *   ESTRELLA     -> rotación alta + margen alto
 *   VACA         -> rotación alta + margen bajo
 *   INTERROGANTE -> rotación baja + margen alto
 *   HUESO        -> rotación baja + margen bajo
 */
class DSSAnalyzer
{
    public const ESTRELLA = 'ESTRELLA';

    public const VACA = 'VACA';

    public const INTERROGANTE = 'INTERROGANTE';

    public const HUESO = 'HUESO';

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

    /* ------------------------------------------------------------------
     | HU-10: Cálculo de tasa de rotación y margen por producto
     | (sin cambios respecto a la entrega anterior)
     | ------------------------------------------------------------------ */

    /**
     * + computeRotationRate(totalSold, periodDays) : float
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
     */
    public function computeMarginRate(float $totalRevenue, float $totalCost): float
    {
        if ($totalRevenue <= 0.0) {
            return 0.0;
        }

        return round(($totalRevenue - $totalCost) / $totalRevenue, 4);
    }

    /**
     * QUERY 3: agrega sale_details + sales por producto en [start, end]
     * para el catálogo de productos activos. Incluye productos sin
     * ventas en el período (total_sold = 0) para que classifyProduct()
     * pueda cubrir el 100% del catálogo (HU-11).
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

    /* ------------------------------------------------------------------
     | HU-11: Clasificación matricial Estrella / Hueso
     | ------------------------------------------------------------------ */

    /**
     * + classifyProduct(rotationRate, marginRate) : string
     *
     * Criterio de Aceptación HU-11:
     *  - ESTRELLA cuando rotación ≥ θ y margen ≥ θ.
     *  - HUESO cuando ambas son menores al umbral.
     *  - VACA / INTERROGANTE cubren los cuadrantes mixtos (matriz BCG
     *    clásica: alta rotación+bajo margen = "vaca lechera"; baja
     *    rotación+alto margen = "interrogante").
     */
    public function classifyProduct(float $rotationRate, float $marginRate): string
    {
        $highRotation = $rotationRate >= $this->rotationThreshold;
        $highMargin = $marginRate >= $this->marginThreshold;

        return match (true) {
            $highRotation && $highMargin => self::ESTRELLA,
            $highRotation && ! $highMargin => self::VACA,
            ! $highRotation && $highMargin => self::INTERROGANTE,
            default => self::HUESO,
        };
    }

    /**
     * + buildStarHusoMatrix(start, end) : array
     *
     * Criterio de Aceptación HU-11:
     *  - Clasifica el 100% del catálogo activo en los cuatro cuadrantes.
     *  - Retorna top_star[5] y critical_huso[5].
     *
     * top_star: los ESTRELLA con mejor combinación rotación+margen
     * (mayor score = mejor desempeño, para priorizar reposición).
     *
     * critical_huso: los HUESO con peor combinación rotación+margen
     * (menor score = más urgente evaluar baja o liquidación), que es
     * el sentido de negocio de "críticos" en el objetivo del proyecto.
     */
    public function buildStarHusoMatrix(Carbon $start, Carbon $end): array
    {
        $stats = $this->getProductStats($start, $end)->map(function (array $row) {
            $row['quadrant'] = $this->classifyProduct($row['rotation_rate'], $row['margin_rate']);

            return $row;
        });

        $quadrants = [
            self::ESTRELLA => [],
            self::VACA => [],
            self::INTERROGANTE => [],
            self::HUESO => [],
        ];

        foreach ($stats as $row) {
            $quadrants[$row['quadrant']][] = $row;
        }

        $score = fn(array $r) => $r['rotation_rate'] + $r['margin_rate'];

        $topStar = collect($quadrants[self::ESTRELLA])
            ->sortByDesc($score)
            ->take($this->rankingSize)
            ->values()
            ->all();

        $criticalHuso = collect($quadrants[self::HUESO])
            ->sortBy($score)
            ->take($this->rankingSize)
            ->values()
            ->all();

        return [
            'quadrants' => $quadrants,
            'top_star' => $topStar,
            'critical_huso' => $criticalHuso,
            'total_classified' => $stats->count(),
        ];
    }

    /* ------------------------------------------------------------------
     | HU-12: Dashboard interactivo con KPIs generales
     | ------------------------------------------------------------------ */

    /**
     * Payload único consumido por GET /api/dashboard (DashboardController).
     *
     * Criterio de Aceptación HU-12: responde con kpis, matrix, top_star,
     * critical_huso e invoice_summary en un único payload JSON.
     */
    public function buildDashboardData(Carbon $start, Carbon $end): array
    {
        $matrix = $this->buildStarHusoMatrix($start, $end);

        return [
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'kpis' => $this->buildKpis($start, $end),
            'matrix' => $matrix['quadrants'],
            'top_star' => $matrix['top_star'],
            'critical_huso' => $matrix['critical_huso'],
            'invoice_summary' => $this->buildInvoiceSummary($start, $end),
        ];
    }

    /**
     * kpis.total_revenue, kpis.total_sales, kpis.active_products
     * (consumidos por <KPICards> en el frontend, HU-12).
     */
    private function buildKpis(Carbon $start, Carbon $end): array
    {
        $sales = Sale::enRango($start, $end)->get(['id', 'total_amount']);

        return [
            'total_revenue' => round((float) $sales->sum('total_amount'), 2),
            'total_sales' => $sales->count(),
            'active_products' => Product::activos()->count(),
        ];
    }

    /**
     * Reutiliza la misma agrupación por invoice_status que HU-09
     * (InvoiceController::summary) para que el Dashboard sea
     * consistente con GET /api/invoices/summary, sin modificar ese
     * controlador ni el modelo Sale.
     */
    private function buildInvoiceSummary(Carbon $start, Carbon $end): array
    {
        $counts = Sale::enRango($start, $end)
            ->selectRaw('invoice_status, COUNT(*) as total')
            ->groupBy('invoice_status')
            ->pluck('total', 'invoice_status');

        return [
            'issued' => (int) ($counts['issued'] ?? 0),
            'pending' => (int) ($counts['pending'] ?? 0),
            'error' => (int) ($counts['error'] ?? 0),
        ];
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
