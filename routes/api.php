<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::middleware('auth')->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store']);
});

