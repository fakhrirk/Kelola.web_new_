<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Auth\CustomPasswordResetController;

Route::get('/', function () {
    return view('auth.login');
});

// Rute untuk Alur Lupa Password Kustom
Route::middleware('guest')->group(function () {
    // Menampilkan form awal untuk lupa password
    Route::get('forgot-password', [CustomPasswordResetController::class, 'showRequestForm'])->name('password.request');

    // Memproses permintaan dari form dan redirect ke halaman status
    Route::post('forgot-password', [CustomPasswordResetController::class, 'handleRequest'])->name('password.email');

    // Halaman status yang akan refresh otomatis
    Route::get('password-reset-status/{token}', [CustomPasswordResetController::class, 'showStatusPage'])->name('password.status');

    // Menampilkan form untuk memasukkan password baru
    Route::get('reset-password/{token}', [CustomPasswordResetController::class, 'showResetForm'])->name('password.reset');

    // Memproses penyimpanan password baru
    Route::post('reset-password', [CustomPasswordResetController::class, 'handleReset'])->name('password.update');
});

// Rute API untuk mengecek status (tidak perlu middleware)
Route::get('password-reset-status/{token}', [CustomPasswordResetController::class, 'checkRequestStatus'])->name('password.status');

// Grup Rute untuk User yang Sudah Login
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'owner') { return redirect()->route('owner.dashboard'); }
        if ($role === 'admin') { return redirect()->route('admin.dashboard'); }
        if ($role === 'karyawan') { return redirect()->route('karyawan.dashboard'); }
        Auth::logout();
        return redirect('/login')->with('error', 'Akses ditolak.');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function() {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/attendance-chart', [OwnerDashboardController::class, 'attendanceChartData'])->name('api.attendance.chart');

        // Route ini sudah benar, pastikan methodnya POST
        Route::post('/password-reset/{id}/approve', [OwnerDashboardController::class, 'approvePasswordReset'])->name('password.reset.approve');
    });

    Route::middleware(['role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function() {
        Route::get('/dashboard', function () { return view('karyawan.dashboard'); })->name('dashboard');
    });
});

require __DIR__.'/auth.php';
