<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::resource('products', ProductController::class);

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockController::class, 'create'])->name('stock.create');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

    Route::get('/restock', [RestockController::class, 'index'])->name('restock.index');
    Route::get('/restock/create', [RestockController::class, 'create'])->name('restock.create');
    Route::post('/restock', [RestockController::class, 'store'])->name('restock.store');
    Route::post('/restock/{restock}/approve', [RestockController::class, 'approve'])->name('restock.approve');
    Route::post('/restock/{restock}/reject', [RestockController::class, 'reject'])->name('restock.reject');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('suppliers', SupplierController::class);
    });
});
