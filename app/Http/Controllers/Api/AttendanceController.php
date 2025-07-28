<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Helpers\LocationHelper;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $officeLat = config('app.office_latitude');
        $officeLon = config('app.office_longitude');
        $radius = config('app.office_radius');

        $distance = LocationHelper::calculateDistance(
            $request->latitude, $request->longitude, $officeLat, $officeLon
        );

        if ($distance > $radius) {
            return response()->json(['message' => 'You are outside the office radius.'], 403);
        }

        $today = Carbon::today()->toDateString();
        $user = Auth::user();

        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'You have already clocked in today.'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'attendance_date' => $today,
            'clock_in' => Carbon::now()->toTimeString(),
            'clock_in_latitude' => $request->latitude,
            'clock_in_longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Clock in successful.', 'data' => $attendance], 201);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Validasi jarak opsional saat clock out, bisa dihapus jika tidak perlu
        $officeLat = config('app.office_latitude');
        $officeLon = config('app.office_longitude');
        $radius = config('app.office_radius');

        $distance = LocationHelper::calculateDistance(
            $request->latitude, $request->longitude, $officeLat, $officeLon
        );

        if ($distance > $radius) {
            return response()->json(['message' => 'You are outside the office radius.'], 403);
        }

        $today = Carbon::today()->toDateString();
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)->first();

        if (!$attendance || $attendance->clock_out) {
            return response()->json(['message' => 'No active clock in found or already clocked out.'], 400);
        }

        $attendance->update([
            'clock_out' => Carbon::now()->toTimeString(),
            'clock_out_latitude' => $request->latitude,
            'clock_out_longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Clock out successful.', 'data' => $attendance]);
    }

    public function history()
    {
        $history = Attendance::where('user_id', Auth::id())
            ->orderBy('attendance_date', 'desc')->get();

        return response()->json($history);
    }
}
