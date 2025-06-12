<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BikeController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::resource('bikes', BikeController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy']);
    
    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent']);

    Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/orders/{bike}', [OrderController::class, 'store']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'register']);
    });
});

