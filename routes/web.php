<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor;
use App\Http\Controllers\Customer;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/vendor', [AuthController::class, 'loginVendor'])->name('login.vendor');
Route::post('/login/customer', [AuthController::class, 'loginCustomer'])->name('login.customer');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========== Vendor Portal ==========
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Vendor\DashboardController::class, 'index'])->name('vendor.dashboard');
    Route::get('/api/stats', [Vendor\DashboardController::class, 'stats'])->name('vendor.api.stats');

    // Orders
    Route::get('/orders/create', [Vendor\OrderController::class, 'create'])->name('vendor.orders.create');
    Route::post('/orders', [Vendor\OrderController::class, 'store'])->name('vendor.orders.store');
    Route::get('/orders/{order}/detail', [Vendor\OrderController::class, 'show'])->name('vendor.orders.show');
    Route::put('/orders/{order}', [Vendor\OrderController::class, 'update'])->name('vendor.orders.update');
    Route::delete('/orders/{order}', [Vendor\OrderController::class, 'destroy'])->name('vendor.orders.destroy');

    // Products
    Route::get('/products', [Vendor\ProductController::class, 'index'])->name('vendor.products.index');
    Route::get('/products/create', [Vendor\ProductController::class, 'create'])->name('vendor.products.create');
    Route::post('/products', [Vendor\ProductController::class, 'store'])->name('vendor.products.store');
    Route::get('/products/{product}/edit', [Vendor\ProductController::class, 'edit'])->name('vendor.products.edit');
    Route::put('/products/{product}', [Vendor\ProductController::class, 'update'])->name('vendor.products.update');
    Route::delete('/products/{product}', [Vendor\ProductController::class, 'destroy'])->name('vendor.products.destroy');

    // Customers
    Route::get('/customers', [Vendor\CustomerController::class, 'index'])->name('vendor.customers.index');

    // Reports
    Route::get('/reports', [Vendor\ReportController::class, 'index'])->name('vendor.reports.index');

    // Settings (profile)
    Route::get('/settings', [ProfileController::class, 'show'])->name('vendor.settings');
    Route::put('/settings', [ProfileController::class, 'update'])->name('vendor.settings.update');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('vendor.settings.password');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('vendor.settings.destroy');
});

// ========== Customer Portal ==========
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Shop / Browse
    Route::get('/shop', [Customer\ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/product/{product}', [Customer\ShopController::class, 'show'])->name('shop.product');

    // Cart
    Route::get('/cart', [Customer\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [Customer\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cartItem}', [Customer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [Customer\CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [Customer\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [Customer\CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order}', [Customer\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // My Orders
    Route::get('/my-orders', [Customer\OrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/my-orders/{order}', [Customer\OrderController::class, 'show'])->name('customer.orders.show');

    // Account
    Route::get('/account', [Customer\AccountController::class, 'index'])->name('customer.account');

    // Settings (profile)
    Route::get('/settings', [ProfileController::class, 'show'])->name('customer.settings');
    Route::put('/settings', [ProfileController::class, 'update'])->name('customer.settings.update');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('customer.settings.password');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('customer.settings.destroy');
});

// Shared auth routes
Route::middleware('auth')->group(function () {
    // Notifications (stub)
    Route::get('/notifications', function () {
        return view('shared.notifications');
    })->name('notifications');

    // Help
    Route::get('/help', function () {
        return view('shared.help');
    })->name('help');
});
