<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PasswordResetRequest;
use App\Models\User; // Pastikan model User di-import
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = User::whereIn('role', ['karyawan', 'admin'])->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $hadirHariIni = Attendance::whereDate('attendance_date', Carbon::today())->count();
        $absensiTerbaru = Attendance::with('user')->latest()->take(5)->get();
        $passwordRequests = PasswordResetRequest::where('status', 'pending')->latest()->get();

        return view('owner.dashboard', compact('totalKaryawan', 'totalAdmin', 'hadirHariIni', 'absensiTerbaru', 'passwordRequests'));
    }

    public function attendanceChartData()
    {
        // ... (Fungsi ini sudah benar, tidak ada perubahan)
        $attendances = Attendance::query()
            ->select(DB::raw('DATE(attendance_date) as date'), DB::raw('count(*) as count'))
            ->where('attendance_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')->orderBy('date', 'asc')->get();
        $labels = $attendances->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $data = $attendances->pluck('count');
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * Menyetujui permintaan dan menghapus password user.
     */
    public function approvePasswordReset($id)
    {
        $resetRequest = PasswordResetRequest::find($id);

        if ($resetRequest && $resetRequest->status === 'pending') {

            // PERBAIKAN UTAMA: Cari user dan hapus passwordnya
            $user = User::where('email', $resetRequest->email)->first();

            if ($user) {
                // Set password user menjadi null di database
                $user->password = null;
                $user->save();
            }

            // Ubah status permintaan menjadi 'approved'
            // Ini akan memicu redirect otomatis di halaman status pengguna
            $resetRequest->status = 'approved';
            $resetRequest->save();

            return back()->with('success', 'Permintaan reset password untuk ' . $resetRequest->email . ' telah disetujui dan password user telah dihapus.');
        }

        return back()->with('error', 'Permintaan tidak ditemukan atau sudah diproses.');
    }
}
