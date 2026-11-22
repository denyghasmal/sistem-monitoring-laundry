<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourierController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1 (Rancang Bangun Pemesanan & Monitoring Laundry)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- Public Routes ---
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Public catalog of services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    // Public / Shared Order Tracking (by ID or Order Code)
    Route::get('/orders/{id}/track', [OrderController::class, 'track']);

    // Payment Gateway Webhook Callback
    Route::post('/payments/callback', [PaymentController::class, 'callback']);

    // --- Protected Routes (Sanctum / Token Auth) ---
    Route::middleware('auth.api')->group(function () {

        // Auth & Profile
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
        });

        // Push Notification FCM Token
        Route::post('/fcm-token', [AuthController::class, 'updateFcmToken']);

        // Customer Addresses CRUD
        Route::apiResource('addresses', AddressController::class);
        Route::patch('addresses/{id}/primary', [AddressController::class, 'setPrimary']);

        // Orders Management & Real-time Tracking
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::post('/orders/{id}/review', [OrderController::class, 'addReview']);

        // Payment Processing
        Route::post('/payments/pay', [PaymentController::class, 'pay']);

        // Courier Routes
        Route::prefix('courier')->group(function () {
            Route::get('/tasks', [CourierController::class, 'tasks']);
        });

        // Admin Only Routes
        Route::middleware('role:admin')->group(function () {
            // Master Services Management
            Route::post('/services', [ServiceController::class, 'store']);
            Route::put('/services/{id}', [ServiceController::class, 'update']);
            Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

            // Courier Assignment
            Route::patch('/orders/{id}/assign-courier', [OrderController::class, 'assignCourier']);
            Route::get('/admin/couriers', [CourierController::class, 'index']);

            // Dashboard & Reporting
            Route::get('/admin/dashboard', [AdminDashboardController::class, 'stats']);
            Route::get('/admin/reports', [AdminDashboardController::class, 'reports']);
        });
    });
});
