<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Redirect root ke dashboard jika sudah login, ke login jika belum
Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Dashboard Routes
    Route::get('/dashboard', function () {
        return spa_view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return spa_view('profile');
    })->name('profile');
    
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin Routes
    Route::middleware(['role:gudang'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return spa_view('admin.dashboard');
        })->name('dashboard');
        
        // User Management Routes
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show', 'create', 'edit']);
    });
});
