<?php

use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ruta de Ventas — HU-07
|--------------------------------------------------------------------------
|
| IMPORTANTE PARA EL MERGE:
| Agregar dentro del grupo middleware('auth:sanctum') existente en
| routes/api.php, junto a las rutas de /products.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sales', [SaleController::class, 'store']);
});
