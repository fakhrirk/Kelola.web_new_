<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Hash tidak lagi diperlukan di sini
// use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = User::whereIn('role', ['karyawan', 'admin'])->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $hadirHariIni = Attendance::whereDate('attendance_date', Carbon::today())->count();
        $absensiTerbaru = Attendance::with('user')->latest()->take(5)->get();

        // Menggunakan nama variabel yang konsisten dengan yang dikirim ke view
        $passwordRequests = PasswordResetRequest::where('status', 'pending')->latest()->get();

        return view('owner.dashboard', compact('totalKaryawan', 'totalAdmin', 'hadirHariIni', 'absensiTerbaru', 'passwordRequests'));
    }

    public function attendanceChartData()
    {
        $attendances = Attendance::query()
            ->select(DB::raw('DATE(attendance_date) as date'), DB::raw('count(*) as count'))
            ->where('attendance_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')->orderBy('date', 'asc')->get();
        $labels = $attendances->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $data = $attendances->pluck('count');
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function approvePasswordReset($id)
    {
        $resetRequest = PasswordResetRequest::find($id);

        if ($resetRequest && $resetRequest->status === 'pending') {
            // DIPERBAIKI: Logika disederhanakan.
            // Tugas Owner hanya menyetujui. Pengguna yang akan mereset passwordnya sendiri.
            // Kita tidak mengubah password user di sini.

            // Ubah status permintaan menjadi 'approved'
            $resetRequest->status = 'approved';
            $resetRequest->save();

            // DIPERBAIKI: Menggunakan 'success' agar cocok dengan view.
            return back()->with('success', 'Permintaan reset password untuk ' . $resetRequest->email . ' telah disetujui.');
        }

        // DIPERBAIKI: Menggunakan 'error' agar cocok dengan view.
        return back()->with('error', 'Permintaan tidak ditemukan atau sudah diproses.');
    }
}
