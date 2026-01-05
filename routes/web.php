<?php

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public routes (no authentication required)
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // GET /products - Browse products
Route::get('/products/{id}', [ProductController::class, 'show']); // GET /products/{id} - View product details
Route::get('/products/filter/price-range', [ProductController::class, 'byPriceRange']); // Filter products by price

// Admin-only routes
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create']); // GET /products/create
    Route::post('/products', [ProductController::class, 'store']); // POST /products - Create product
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit'); // GET /products/{id}/edit
    Route::post('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // PUT /products/{id} - Update product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // DELETE /products/{id} - Delete product
    Route::post('/products/{id}/notify-low-stock', [ProductController::class, 'sendLowStockNotification']); // Send low stock notification
    Route::get('/products/analytics/statistics', [ProductController::class, 'statistics']); // Admin analytics

    Route::get('/orders/all', [OrderController::class, 'allOrders']); // View all orders (admin only)
});

// Authenticated user routes (customers can view stock, admins can update)
Route::middleware(['auth'])->group(function () {
    Route::get('/products/stock/low', [ProductController::class, 'lowStock']); // View low stock products
    Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock'])->middleware('admin'); // Update stock (admin only)

    Route::get('/orders', [OrderController::class, 'userOrders'])->name('orders.index'); 
    Route::post('/order', [OrderController::class, 'placeOrder']); // Place order
    Route::get('/order/{id}', [OrderController::class, 'viewOrder']); // View order details
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder']);
});

Route::get('/order/preview', [OrderController::class, 'previewOrder']);


// Product CRUD Routes
// Route::prefix('api')->group(function () {});

// For testing purposes
Route::get('/test/email', function () {
    $product = Product::first();
    return view('emails.low-stock-alert', [
        'product' => $product,
        'stockLevel' => $product->stock,
        'isOutOfStock' => $product->stock === 0,
        'productUrl' => url("/products/{$product->id}"),
        'adminDashboardUrl' => url('/admin/products'),
    ]);
});

require __DIR__.'/auth.php';
