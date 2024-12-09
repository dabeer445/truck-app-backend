<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Livewire\Admin\Chat;
use App\Livewire\Actions\Logout;
use App\Livewire\Counter;


Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    Route::post('logout', Logout::class)->name('logout');
});

Route::middleware('\Spatie\Permission\Middleware\RoleMiddleware:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [DashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/chat', [DashboardController::class, 'chat'])->name('chat');
    // Route::get('/chat', [Chat::class, 'chat'])->name('chat');
    // Route::get('/chat', Chat::class)->name('chat'); // Changed this line
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
});

 
Route::get('/counter', Counter::class);


require __DIR__ . '/auth.php';
