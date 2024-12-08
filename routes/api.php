<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // Order routes
        Route::apiResource('orders', OrderController::class);
        Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);
        
        // User routes
        Route::get('/user/profile', [UserController::class, 'profile']);
        Route::put('/user/profile', [UserController::class, 'update']);

        // Admin routes
        Route::prefix('admin')->middleware('\Spatie\Permission\Middleware\RoleMiddleware:admin')->group(function () {
            Route::get('/orders', [AdminController::class, 'orders']);
            Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus']);
        });
    });
});