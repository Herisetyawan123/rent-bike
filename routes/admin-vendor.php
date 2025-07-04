<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\{
    DashboardController,
    MotorController,
    TransactionController,
    ContractController,
    ReportController,
    ProfileController,
    NotificationController,
    VendorAuthController
};
use App\Models\Vendor;

Route::get('login', [VendorAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [VendorAuthController::class, 'login']);
Route::get('register', [VendorAuthController::class, 'showRegisterForm'])->name('vendor.register');
Route::post('register', [VendorAuthController::class, 'register']);

Route::prefix('admin-vendor')
    ->middleware(['auth'])
    ->name('admin-vendor.')
    ->group(function () {

    // Dashboard - single page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Resource routes
    Route::resource('motors', MotorController::class);
    Route::get('motors/draft', [MotorController::class, 'draft'])->name('motors.draft');
    Route::get('motors/trash', function() {})->name('motors.trash');
    Route::resource('transactions', TransactionController::class);
    Route::get(
        'transactions/{transaction}/contract',
        [TransactionController::class, 'downloadContract']
    )->name('transactions.contract');
    Route::resource('contracts', ContractController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('profiles', ProfileController::class);
    Route::resource('notifications', NotificationController::class);

    Route::get('/edit', [VendorAuthController::class, 'edit'])->name('edit');
    Route::put('/update', [VendorAuthController::class, 'update'])->name('update');
});
