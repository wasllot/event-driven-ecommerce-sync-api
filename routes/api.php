<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected sync routes
Route::middleware('auth.api_token')->group(function () {
    Route::post('/sync/product', [SyncController::class, 'syncProduct']);
    Route::post('/sync/order', [OrderController::class, 'replicateOrder']);

    // Retailer Migration Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/migrate', [ProductController::class, 'migrate']);
});
