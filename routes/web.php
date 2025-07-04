<?php

use App\Http\Controllers\Vendor\VendorAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [VendorAuthController::class, 'logout'])->name('logout');
});

require __DIR__.'/admin-vendor.php';