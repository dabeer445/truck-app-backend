<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MessageController;

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

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::patch('{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('{id}', [NotificationController::class, 'destroy']);
        });

        // Message Routes
        Route::prefix('messages')->group(function () {
            Route::get('/', [MessageController::class, 'index']);
            Route::post('/', [MessageController::class, 'store']);
            Route::patch('{id}/read', [MessageController::class, 'markAsRead']);
            Route::get('unread-count', [MessageController::class, 'getUnreadCount']);
            Route::get('orders/{order}/conversation', [MessageController::class, 'getConversation']);
        });

    });
});
