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
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);    
    
    Route::resource('bikes', BikeController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
    
    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/contract/{id}/download', [OrderController::class, 'downloadContract']);
        Route::post('/orders/{bike}', [OrderController::class, 'store']);
        Route::post('/logout', [AuthController::class, 'register']);
        Route::get('/eligibility', [AuthController::class, 'checkEligibility']);

        // update user profile
        Route::get('/profile', [AuthController::class, 'profile']);    
        Route::put('/profile', [AuthController::class, 'update']);    
        Route::post('/profile/upload-national-id', [AuthController::class, 'uploadNationalId']);
        Route::post('/profile/upload-driver-license', [AuthController::class, 'uploadDrivingLicense']);
        Route::post('/profile/upload-selfie-with-id', [AuthController::class, 'uploadSelfieWithId']);
    });
});

