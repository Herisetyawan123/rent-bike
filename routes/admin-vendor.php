<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\{
    DashboardController,
    MotorController,
    TransactionController,
    ContractController,
    ReportController,
    ProfileController,
    NotificationController
};

Route::prefix('admin-vendor')
    // ->middleware(['auth', 'role:vendor'])
    ->name('admin-vendor.')
    ->group(function () {

    // Dashboard - single page
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource routes
    Route::resource('motors', MotorController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('profiles', ProfileController::class);
    Route::resource('notifications', NotificationController::class);
});
