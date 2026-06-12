<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminMealController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/meals',     [MealController::class, 'index']);
Route::get('/meals/{id}',[MealController::class, 'show']);
Route::get('/plans',     [PlanController::class, 'index']);
Route::get('/plans/{id}',[PlanController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',           [AuthController::class, 'logout']);
    Route::get('/me',                [AuthController::class, 'me']);
    Route::get('/cart',              [CartController::class, 'index']);
    Route::post('/cart',             [CartController::class, 'addItem']);
    Route::put('/cart/{itemId}',     [CartController::class, 'updateItem']);
    Route::delete('/cart/{itemId}',  [CartController::class, 'removeItem']);
    Route::delete('/cart',           [CartController::class, 'clearCart']);
    Route::get('/orders',            [OrderController::class, 'index']);
    Route::get('/orders/{id}',       [OrderController::class, 'show']);
    Route::post('/orders',           [OrderController::class, 'store']);
    Route::get('/subscriptions',     [SubscriptionController::class, 'index']);
    Route::post('/subscriptions',    [SubscriptionController::class, 'store']);
    Route::post('/payments',         [PaymentController::class, 'store']);
    // Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Meals
    Route::post('/meals', [AdminMealController::class, 'store']);
    Route::put('/meals/{id}', [AdminMealController::class, 'update']);
    Route::delete('/meals/{id}', [AdminMealController::class, 'destroy']);

    // Plans
    Route::post('/plans', [AdminPlanController::class, 'store']);
    Route::put('/plans/{id}', [AdminPlanController::class, 'update']);
    Route::delete('/plans/{id}', [AdminPlanController::class, 'destroy']);

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    // Users
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
});
});