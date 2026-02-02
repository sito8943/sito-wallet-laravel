<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Currencies
    Route::get('/currencies', [CurrencyController::class, 'index']);
    Route::post('/currencies', [CurrencyController::class, 'store']);
    Route::get('/currencies/{currency}', [CurrencyController::class, 'show']);
    Route::patch('/currencies/{currency}', [CurrencyController::class, 'update']);
    Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy']);

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{account}', [AccountController::class, 'show']);
    Route::patch('/accounts/{account}', [AccountController::class, 'update']);
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy']);

    // Transaction Categories
    Route::get('/transaction-categories', [TransactionCategoryController::class, 'index']);
    Route::post('/transaction-categories', [TransactionCategoryController::class, 'store']);
    Route::get('/transaction-categories/{transactionCategory}', [TransactionCategoryController::class, 'show']);
    Route::patch('/transaction-categories/{transactionCategory}', [TransactionCategoryController::class, 'update']);
    Route::delete('/transaction-categories/{transactionCategory}', [TransactionCategoryController::class, 'destroy']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::patch('/transactions/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);
})
;
