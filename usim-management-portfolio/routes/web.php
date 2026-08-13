<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UsimController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('devices', DeviceController::class)->except(['show']);

    Route::get('/usims/export', [UsimController::class, 'export'])->name('usims.export');
    Route::get('/usims/import', [UsimController::class, 'importForm'])->name('usims.import.form');
    Route::post('/usims/import', [UsimController::class, 'import'])->name('usims.import');
    Route::resource('usims', UsimController::class);

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});
