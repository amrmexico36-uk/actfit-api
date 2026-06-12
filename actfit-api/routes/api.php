<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/meals',       [MealController::class, 'index']);
Route::get('/meals/{id}',  [MealController::class, 'show']);

Route::get('/plans',       [PlanController::class, 'index']);
Route::get('/plans/{id}',  [PlanController::class, 'show']);

// ─── Protected Routes (require login) ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Cart
    Route::get('/cart',              [CartController::class, 'index']);
    Route::post('/cart',             [CartController::class, 'addItem']);
    Route::put('/cart/{itemId}',     [CartController::class, 'updateItem']);
    Route::delete('/cart/{itemId}',  [CartController::class, 'removeItem']);
    Route::delete('/cart',           [CartController::class, 'clearCart']);

    // Orders
    Route::get('/orders',       [OrderController::class, 'index']);
    Route::get('/orders/{id}',  [OrderController::class, 'show']);
    Route::post('/orders',      [OrderController::class, 'store']);   // checkout from cart

    // Subscriptions
    Route::get('/subscriptions',    [SubscriptionController::class, 'index']);
    Route::post('/subscriptions',   [SubscriptionController::class, 'store']);

    // Payments
    Route::post('/payments',        [PaymentController::class, 'store']);
});
