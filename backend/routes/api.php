<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductCategoryController;

// Registra la ruta de productos (y las demás si ya tienes sus controladores)
Route::apiResource('products', ProductController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', ProductCategoryController::class);