<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Product CRUD Routes
Route::prefix('api')->group(function () {
    // Route::resource('products', ProductController::class); // Using manual routes below instead of resource for clarity
    
    Route::get('/products', [ProductController::class, 'index']); // GET /api/products
    Route::get('/products/create', [ProductController::class, 'create']); // GET /api/products/create
    Route::post('/products', [ProductController::class, 'store']); // POST /api/products
    Route::get('/products/{id}', [ProductController::class, 'show']); // GET /api/products/{id}
    Route::get('/products/{id}/edit', [ProductController::class, 'edit']); // GET /api/products/{id}/edit
    Route::put('/products/{id}', [ProductController::class, 'update']); // PUT /api/products/{id}
    Route::patch('/products/{id}', [ProductController::class, 'update']); // PATCH /api/products/{id}
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // DELETE /api/products/{id}
    
    // Additional custom routes
    Route::get('/products/stock/low', [ProductController::class, 'lowStock']);
    Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
    Route::get('/products/filter/price-range', [ProductController::class, 'byPriceRange']);
    Route::get('/products/analytics/statistics', [ProductController::class, 'statistics']);
});

