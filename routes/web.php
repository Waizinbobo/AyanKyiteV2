<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;

// API Routes for Table Management
Route::prefix('admin')->group(function () {
    Route::get('/tables', [TableController::class, 'index']);
    Route::post('/tables', [TableController::class, 'store']);
    Route::put('/tables/{id}', [TableController::class, 'update']);
    Route::delete('/tables/{id}', [TableController::class, 'destroy']);
    Route::patch('/tables/{id}/status', [TableController::class, 'updateStatus']);
    Route::get('/tables/stats', [TableController::class, 'stats']);
    Route::get('/tables/{tableNumber}/qr', [TableController::class, 'getQRCode']);
});

// API Routes for Category Management
Route::prefix('admin')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// API Routes for Menu Management
Route::prefix('admin')->group(function () {
    Route::get('/menus', [MenuController::class, 'index']);
    Route::post('/menus', [MenuController::class, 'store']);
    Route::put('/menus/{id}', [MenuController::class, 'update']);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    Route::get('/menus/categories/list', [MenuController::class, 'categoriesList']);
});

// Admin routes
Route::get('/admin/{any?}', function () {
    return view('admin');
})->where('any', '.*');

// Main app routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
