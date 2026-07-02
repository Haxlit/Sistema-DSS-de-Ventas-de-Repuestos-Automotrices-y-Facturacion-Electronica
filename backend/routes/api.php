<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductCategoryController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (HU-01 / HU-02)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación (HU-04 / HU-05)
|--------------------------------------------------------------------------
| Todo el catálogo y gestión requiere sesión (vendedor o admin).
*/
Route::middleware('auth:sanctum')->group(function () {

    // Rutas específicas de productos protegidas
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Si necesitas que HU-06 (index/show) sea pública, déjalas fuera. 
    // Si deben ser privadas, puedes cambiar el apiResource de abajo o meterlo aquí con un 'except' o 'only'.
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Rol de Administrador (HU-03)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Rutas de Recursos del Sistema (Públicas o Ajustadas)
|--------------------------------------------------------------------------
*/
// NOTA: Quitamos 'products' de aquí para que no duplique ni haga públicas las rutas de la HU-04/05.
// Si necesitas el GET (index/show) de productos público, descomenta la siguiente línea:
// Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/{product}', [ProductController::class, 'show']);

Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', ProductCategoryController::class);


/*
|--------------------------------------------------------------------------
| Rutas de Facturación Electrónica — HU-08 (emisión) y HU-09 (consolidado)
|--------------------------------------------------------------------------
|
| IMPORTANTE PARA EL MERGE:
| Agregar dentro del grupo middleware('auth:sanctum') existente en
| routes/api.php, junto a las rutas de /products y /sales.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sales/{sale}/issue-invoice', [InvoiceController::class, 'issue']);
    Route::get('/invoices/summary', [InvoiceController::class, 'summary']);
});