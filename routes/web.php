<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

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

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware(['role:gudang'])->group(function () {
        Route::get('/admin/bahan-baku', [BahanBakuController::class, 'index'])->name('admin.bahan_baku');
    });

    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'gudang') {
            return app(AdminDashboardController::class)();
        }

        if ($user->role === 'dapur') {
            return app(UserDashboardController::class)();
        }

        return abort(403, 'Role tidak dikenali');
    })->name('dashboard');

    // Dashboard Admin (gudang)
    Route::middleware(['role:gudang'])->group(function () {
        Route::get('/home', function () {
            return redirect()->route('dashboard');
        })->name('home');

        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('bahan_baku', BahanBakuController::class)->except(['create', 'edit']);
            Route::get('permintaan', [PermintaanController::class, 'indexGudang'])->name('permintaan.index');
            Route::post('permintaan/{id}/setujui', [PermintaanController::class, 'setujuiPermintaan'])->name('permintaan.setujui');
            Route::post('permintaan/{id}/tolak', [PermintaanController::class, 'tolakPermintaan'])->name('permintaan.tolak');
        });
    });

    // Dashboard User (dapur)
    Route::middleware(['role:dapur'])->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('permintaan', [PermintaanController::class, 'indexUser'])->name('permintaan.index');
            Route::get('permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
            Route::post('permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
        });
    });
});
