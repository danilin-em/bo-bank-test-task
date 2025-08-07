<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);

Route::get('/accounts/{id}/balance', [AccountController::class, 'getBalance']);
Route::get('/accounts/{id}/transactions', [AccountController::class, 'getTransactions']);
Route::post('/accounts/{id}/deposit', [AccountController::class, 'deposit']);
