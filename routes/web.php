<?php

use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PayPalPaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Integration\IntegrationController;
use App\Http\Controllers\Integration\PaymentIntegrationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreController;
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

Route::prefix('store/{storeSlug}')->group(function () {

    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('checkout', [\App\Http\Controllers\CustomerOrderController::class, 'create'])->name('checkout.create');
    Route::post('checkout', [\App\Http\Controllers\CustomerOrderController::class, 'store'])->name('checkout.store');
    Route::get('checkout/pay/{order_id}', [\App\Http\Controllers\CustomerOrderController::class, 'pay'])->name('checkout.pay');
    
    Route::prefix('payments/paypal')->name('payments.paypal.')->group(function () {
        Route::post('create/{order}', [PayPalPaymentController::class, 'createOrder'])->name('create');
        Route::get('capture', [PayPalPaymentController::class, 'capture'])->name('capture');
        Route::get('cancel', [PayPalPaymentController::class, 'cancel'])->name('cancel');
    });
    
    Route::get('products/{slug}', [\App\Http\Controllers\ShopController::class, 'show'])->name('store.products.show')->where('slug', '.*'); // Allow encrypted slug format

    Route::get('orders', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('orders/{slug}', [\App\Http\Controllers\CustomerOrderController::class, 'show'])->name('customer.orders.show');
});

Route::middleware(['auth'])->group(function () {
    // Integrations
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/', [IntegrationController::class, 'index'])->name('index');
        
        // Payment Integrations
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/{provider}/configure', [PaymentIntegrationController::class, 'configure'])->name('configure');
        });

        Route::prefix('paypal')->name('paypal.')->group(function () {
            Route::get('/onboarding', [\App\Http\Controllers\Integration\PaypalController::class, 'onboarding'])->name('onboarding');
            Route::get('/return', [\App\Http\Controllers\Integration\PaypalController::class, 'handleReturn'])->name('onboarding.return');
        });
    });

    // Checkout & Orders
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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/meta.php';
