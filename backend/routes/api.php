<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\Api\AuthController;

// Registra la ruta de productos (y las demás si ya tienes sus controladores)
Route::apiResource('products', ProductController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', ProductCategoryController::class);

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación — HU-02 (Login / Logout)
|--------------------------------------------------------------------------
|
| IMPORTANTE PARA EL MERGE:
| Combinar dentro del mismo grupo Route::prefix('auth') que ya creó
| la Persona 1 en HU-01. El resultado final en routes/api.php debe
| verse así:
|
| Route::prefix('auth')->group(function () {
|     Route::post('/register', [AuthController::class, 'register']);
|     Route::post('/login', [AuthController::class, 'login']);
|
|     Route::middleware('auth:sanctum')->group(function () {
|         Route::post('/logout', [AuthController::class, 'logout']);
|     });
| });
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
