<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard page with real database metrics.
     */
    public function index()
    {
        $today = now()->toDateString();

        $totalEmployees = User::where('role', 'user')->count();

        $presentToday = AttendanceLog::whereDate('date', $today)
            ->where('type', 'MASUK')
            ->whereIn('status', ['TEPAT_WAKTU', 'TERLAMBAT'])
            ->distinct('user_id')
            ->count('user_id');

        $lateToday = AttendanceLog::whereDate('date', $today)
            ->where('type', 'MASUK')
            ->where('status', 'TERLAMBAT')
            ->count();

        $outOfRadiusToday = AttendanceLog::whereDate('date', $today)
            ->where('status', 'LUAR_RADIUS')
            ->count();

        $primaryGeofence = GeofenceSetting::where('is_active', true)->first()
            ?? GeofenceSetting::first();

        $perPage = request('per_page', 5);
        $recentLogs = AttendanceLog::with('user', 'geofence')
            ->latest('date')
            ->latest('time')
            ->paginate($perPage)->withQueryString();

        $mapLogs = AttendanceLog::with('user')
            ->latest('date')
            ->latest('time')
            ->take(30)
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'lateToday',
            'outOfRadiusToday',
            'primaryGeofence',
            'recentLogs',
            'mapLogs'
        ));
    }
}
