<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return view('welcome');
    });

    if (app()->environment('local')) {
        Route::get('/style-guide', function () {
            return view('style-guide');
        });
    }

    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{product}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::patch('/inventory/{product}/price', [InventoryController::class, 'updatePrice'])->name('inventory.price');
    Route::patch('/inventory/{product}/levels', [InventoryController::class, 'updateLevels'])->name('inventory.levels');
});
