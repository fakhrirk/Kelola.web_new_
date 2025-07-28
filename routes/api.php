<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
// Hapus import yang tidak terpakai jika ada
// use App\Http\Controllers\Api\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardWebController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Fitur Karyawan (bisa diakses semua role yang login)
    Route::middleware('role:karyawan,admin,owner')->prefix('attendance')->group(function () {
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('/history', [AttendanceController::class, 'history']);
    });

    // Fitur Admin & Owner (Kelola Karyawan)
    Route::middleware('role:admin,owner')->prefix('manage')->group(function () {
        Route::apiResource('users', AdminUserController::class);
    });

    // Fitur API Khusus Admin (Grafik)
    Route::middleware('role:admin,owner')->prefix('admin/api')->group(function() {
        Route::get('/attendance-chart', [AdminDashboardWebController::class, 'attendanceChartData'])->name('admin.api.attendance.chart');
    });

    // ▼▼▼ BAGIAN INI DIHAPUS ▼▼▼
    // Fitur API Khusus Owner (Grafik)
});
