<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
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

// Protected routes
Route::middleware('auth')->group(function () {
    // Vendor dashboard (READ all orders)
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');

    // Order CRUD
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Tracking
    Route::get('/track', [OrderController::class, 'track'])->name('orders.track.form');
    Route::post('/track', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/track/{orderCode}', [OrderController::class, 'track'])->name('orders.track');

    // Update status (UPDATE)
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

    // Delete order (DELETE)
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});
