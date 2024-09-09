<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

Route::middleware(['sanctum', 'throttle:api', 'substitutebindings'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/deposit', [PaymentController::class, 'deposit']);
        Route::post('/withdraw', [PaymentController::class, 'withdraw']);
        Route::post('/transfer', [PaymentController::class, 'transfer']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::get('/balance', [AccountController::class, 'showBalance']);
        Route::get('/transactions', [AccountController::class, 'recentTransactions']);
        Route::get('/dashboard', [AccountController::class, 'dashboard']);
    });
});
