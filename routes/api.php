<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/upload', [UploadController::class, 'upload']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);

Route::apiResource('products', ProductController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);


/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [AuthController::class, 'profile']);

    Route::put('/profile', [AuthController::class, 'updateProfile']);


    Route::apiResource('cart', CartController::class)
        ->only([
            'index',
            'store',
            'update',
            'destroy'
        ]);


    Route::apiResource('wishlist', WishlistController::class)
        ->only([
            'index',
            'store',
            'destroy'
        ]);


    Route::apiResource('orders', OrderController::class)
        ->only([
            'index',
            'store',
            'show',
            'update'
        ]);
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ]);


        Route::apiResource('orders', AdminOrderController::class)
            ->only([
                'index',
                'show',
                'update'
            ]);


        Route::apiResource('products', AdminProductController::class)
            ->only([
                'index',
                'show',
                'update',
                'destroy'
            ]);


        Route::apiResource('categories', AdminCategoryController::class)
            ->only([
                'index',
                'show',
                'update',
                'destroy'
            ]);
    });


/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/

Route::post('/test', function () {
    dd('API TEST');
});