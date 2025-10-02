<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use \App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\ProfileController;

// Redirect root ke dashboard sesuai role jika sudah login, ke login jika belum
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    $user = Auth::user();
    if (in_array($user->role, ['gudang', 'dapur'], true)) {
        return redirect()->route('dashboard');
    }
    return abort(403, 'Role tidak dikenali');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/admin/bahan-baku', [BahanBakuController::class, 'index'])->name('admin.bahan_baku');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'gudang') {
            return spa_view('admin.dashboard');
        }

        if ($user->role === 'dapur') {
            return view('user.dashboard');
        }

        return abort(403, 'Role tidak dikenali');
    })->name('dashboard');

    // Dashboard Admin (gudang)
    Route::middleware(['role:gudang'])->group(function () {
        Route::get('/home', function () {
            return redirect()->route('dashboard');
        })->name('home');

        Route::get('/profile', function () {
            return spa_view('profile');
        })->name('profile');

        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('bahan_baku', BahanBakuController::class)->except(['create', 'edit']);
        });
    });

    // Dashboard User (dapur)
    Route::middleware(['role:dapur'])->group(function () {
        // belum ada
    });
});
