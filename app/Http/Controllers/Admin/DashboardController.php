<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetApproved;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $hadirHariIni = Attendance::whereDate('attendance_date', Carbon::today())->count();
        $absensiBulanIni = Attendance::whereMonth('created_at', Carbon::now()->month)->count();
        $absensiTerbaru = Attendance::with('user')->latest()->take(5)->get();
        return view('admin.dashboard', compact('totalKaryawan', 'hadirHariIni', 'absensiBulanIni', 'absensiTerbaru'));
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


}
