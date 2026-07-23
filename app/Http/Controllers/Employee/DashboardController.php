<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the employee attendance dashboard page.
     */
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $primaryGeofence = GeofenceSetting::where('is_active', true)->first() 
            ?? GeofenceSetting::first();

        $checkInLog = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->where('type', 'MASUK')
            ->first();

        $checkOutLog = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->where('type', 'PULANG')
            ->first();

        $statusText = 'Belum Presensi';
        if ($checkOutLog) {
            $statusText = 'Sudah Presensi Pulang';
        } elseif ($checkInLog) {
            $statusText = 'Sudah Presensi Masuk';
        }

        $todayStatus = [
            'status' => $statusText,
            'check_in' => $checkInLog ? $checkInLog->time : null,
            'check_out' => $checkOutLog ? $checkOutLog->time : null,
            'date_formatted' => now()->translatedFormat('l, d F Y'),
        ];

        $geofence = [
            'office_name' => $primaryGeofence->office_name ?? 'Kantor Utama',
            'office_lat' => $primaryGeofence->lat ?? -6.208800,
            'office_lng' => $primaryGeofence->lng ?? 106.845600,
            'radius_meters' => $primaryGeofence->radius_in_meters ?? 50,
            'shift_start' => $primaryGeofence->shift_start ?? '08:00',
            'shift_end' => $primaryGeofence->shift_end ?? '17:00',
        ];

        $presentCount = AttendanceLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('type', 'MASUK')
            ->whereIn('status', ['TEPAT_WAKTU', 'TERLAMBAT'])
            ->count();

        $lateCount = AttendanceLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('type', 'MASUK')
            ->where('status', 'TERLAMBAT')
            ->count();

        $outOfRadiusCount = AttendanceLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'LUAR_RADIUS')
            ->count();

        $totalLogsCount = AttendanceLog::where('user_id', $user->id)->count();

        $employeeStats = [
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'out_of_radius_count' => $outOfRadiusCount,
            'total_logs_count' => $totalLogsCount,
        ];

        $recentLogs = AttendanceLog::where('user_id', $user->id)
            ->with('geofence')
            ->latest('date')
            ->latest('time')
            ->take(10)
            ->get();

        return view('employee.dashboard', compact('user', 'todayStatus', 'geofence', 'employeeStats', 'recentLogs'));
    }
}
