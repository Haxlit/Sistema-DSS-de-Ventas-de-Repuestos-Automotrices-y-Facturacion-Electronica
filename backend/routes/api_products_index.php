<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ruta de Búsqueda de Catálogo — HU-06
|--------------------------------------------------------------------------
|
| IMPORTANTE PARA EL MERGE:
| Agregar esta línea dentro del MISMO grupo middleware('auth:sanctum')
| que ya crearon HU-04/HU-05 para /products, junto a store/update/destroy.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});
