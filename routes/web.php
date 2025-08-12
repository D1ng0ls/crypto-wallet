<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('crypto')->controller(WalletController::class)->group(function () {
        Route::post('/buy', [WalletController::class, 'buy'])->name('crypto.buy');
        Route::post('/send', [WalletController::class, 'transfer'])->name('crypto.send');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('crypto.withdraw');
    });

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::put('/security', [ProfileController::class, 'editPassword'])->name('profile.security');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});