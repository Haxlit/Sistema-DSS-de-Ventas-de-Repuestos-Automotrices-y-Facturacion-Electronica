<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\Api\DashboardController;


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
// Registra la ruta de productos (y las demás si ya tienes sus controladores)
Route::apiResource('products', ProductController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', ProductCategoryController::class);