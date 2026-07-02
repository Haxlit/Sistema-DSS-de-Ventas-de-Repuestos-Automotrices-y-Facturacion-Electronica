<?php

/**
 * HU-10: Cálculo de tasa de rotación y margen por producto
 *
 * Centraliza los umbrales de negocio usados por App\Services\DSSAnalyzer
 * para no dejarlos escritos en duro en el código (criterio de aceptación
 * HU-10: "rotationThreshold y marginThreshold son configurables").
 * Pueden sobrescribirse por variable de entorno sin tocar código.
 */

return [

    // Rotación mínima (unidades vendidas por día) para considerar un
    // producto de "alta rotación" en la matriz Estrella/Hueso (HU-11).
    'rotation_threshold' => (float) env('DSS_ROTATION_THRESHOLD', 0.5),

    // Margen de ganancia neta mínimo (0.20 = 20%) para considerar un
    // producto de "alto margen" en la matriz Estrella/Hueso (HU-11).
    'margin_threshold' => (float) env('DSS_MARGIN_THRESHOLD', 0.20),

    // Rango de análisis por defecto (en días) cuando el cliente no
    // especifica start/end, consistente con el resto de los KPIs del
    // Dashboard (mismo criterio que HU-09: "últimos 30 días").
    'default_range_days' => (int) env('DSS_DEFAULT_RANGE_DAYS', 30),

    // Tamaño de los rankings top_star / critical_huso expuestos en el
    // Dashboard (HU-11, HU-12).
    'ranking_size' => (int) env('DSS_RANKING_SIZE', 5),

];
