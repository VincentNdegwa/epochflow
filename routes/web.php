<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Auth\CustomerAuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/store/products/{product:slug}', [ShopController::class, 'show'])->name('shop.products.show');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('stores.show');

// Customer Auth Routes
Route::prefix('store/{storeSlug}')->name('customer.')->group(function () {
    Route::get('login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [CustomerAuthController::class, 'login'])->name('login.attempt');
    Route::get('register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [CustomerAuthController::class, 'register'])->name('register.attempt');
    Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
});


Route::middleware(['auth'])->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout & Orders
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{slug}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Product Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Customer Management
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{slug}', [CustomerController::class, 'show'])->name('customers.show');

    // Store Management
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [StoreController::class, 'store'])->name('stores.store');
    Route::get('/stores/{slug}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{slug}', [StoreController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{slug}', [StoreController::class, 'destroy'])->name('stores.destroy');

    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/meta.php';
