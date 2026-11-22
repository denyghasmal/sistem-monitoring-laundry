<?php

use App\Http\Controllers\Web\CustomerWebController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Default landing page goes to Customer Web Portal
Route::get('/', function () {
    return redirect()->route('customer.index');
});

// Customer Web Portal
Route::prefix('portal')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('customer.index');
    Route::get('/pesan', [CustomerWebController::class, 'orderPage'])->name('customer.order');
    Route::post('/pesan', [CustomerWebController::class, 'placeOrder'])->name('customer.order.post');
    Route::get('/lacak/{code?}', [CustomerWebController::class, 'track'])->name('customer.track');
    Route::get('/riwayat', [CustomerWebController::class, 'history'])->name('customer.history');
});

// Admin Operations Dashboard
Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/orders/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::post('/orders/{id}/assign-courier', [DashboardController::class, 'assignCourier'])->name('admin.orders.assign');
    Route::post('/services', [DashboardController::class, 'storeService'])->name('admin.services.store');
});

// Mobile App Interactive Simulator & API Docs
Route::get('/simulator', [DashboardController::class, 'simulator'])->name('mobile.simulator');
Route::get('/api-docs', [DashboardController::class, 'apiDocs'])->name('api.docs');
