<?php

namespace App\Providers;

use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // View Composer for Admin Navbar Notifications
        View::composer('admin.layouts.navbar', function ($view) {
            $notifications = [];

            // 1. Alert Out of Radius
            $outOfRadiusCount = AttendanceLog::whereDate('date', today())
                ->where('status', 'LUAR_RADIUS')
                ->count();
            if ($outOfRadiusCount > 0) {
                $notifications[] = [
                    'icon' => 'bi-geo-alt-fill text-danger',
                    'title' => "{$outOfRadiusCount} Presensi di Luar Radius",
                    'subtitle' => 'Perlu verifikasi lokasi geofence',
                    'time' => 'Hari Ini',
                    'link' => route('admin.attendance_logs.index'),
                ];
            }

            // 2. Pending Biometric
            $pendingBioCount = User::where('account_status', 'Pending Biometrik')->count();
            if ($pendingBioCount > 0) {
                $notifications[] = [
                    'icon' => 'bi-person-exclamation text-warning',
                    'title' => "{$pendingBioCount} Karyawan Pending Biometrik",
                    'subtitle' => 'Belum mendaftarkan data sampel foto wajah',
                    'time' => 'Terbaru',
                    'link' => route('admin.employees.index'),
                ];
            }

            // 3. Late Arrivals Today
            $lateCount = AttendanceLog::whereDate('date', today())
                ->where('status', 'TERLAMBAT')
                ->count();
            if ($lateCount > 0) {
                $notifications[] = [
                    'icon' => 'bi-clock-history text-info',
                    'title' => "{$lateCount} Karyawan Terlambat Masuk",
                    'subtitle' => 'Toleransi keterlambatan terlampaui',
                    'time' => 'Hari Ini',
                    'link' => route('admin.attendance_logs.index'),
                ];
            }

            // Default system notification if empty
            if (empty($notifications)) {
                $notifications[] = [
                    'icon' => 'bi-check-circle-fill text-success',
                    'title' => 'Sistem Presensi Normal',
                    'subtitle' => 'Semua layanan geofence & biometrik berjalan lancar',
                    'time' => 'Baru Saja',
                    'link' => '#',
                ];
            }

            $view->with('adminNotifications', $notifications);
        });

        // View Composer for Employee Navbar Notifications
        View::composer('employee.layouts.navbar', function ($view) {
            $notifications = [];
            $user = Auth::user();

            if ($user) {
                $todayLog = AttendanceLog::where('user_id', $user->id)
                    ->whereDate('date', today())
                    ->get();

                $checkIn = $todayLog->where('type', 'MASUK')->first();
                $checkOut = $todayLog->where('type', 'PULANG')->first();

                if ($checkIn) {
                    $notifications[] = [
                        'icon' => 'bi-box-arrow-in-right text-success',
                        'title' => 'Presensi Masuk Berhasil',
                        'subtitle' => 'Tercatat pada pukul ' . $checkIn->time,
                        'time' => 'Hari Ini',
                    ];
                } else {
                    $notifications[] = [
                        'icon' => 'bi-exclamation-circle-fill text-warning',
                        'title' => 'Belum Presensi Masuk',
                        'subtitle' => 'Jangan lupa melakukan scan wajah & GPS',
                        'time' => 'Pengingat Hari Ini',
                    ];
                }

                if ($checkOut) {
                    $notifications[] = [
                        'icon' => 'bi-box-arrow-left text-info',
                        'title' => 'Presensi Pulang Berhasil',
                        'subtitle' => 'Tercatat pada pukul ' . $checkOut->time,
                        'time' => 'Hari Ini',
                    ];
                }

                if ($user->account_status === 'Pending Biometrik') {
                    $notifications[] = [
                        'icon' => 'bi-camera-fill text-danger',
                        'title' => 'Registrasi Biometrik Wajah',
                        'subtitle' => 'Lakukan pendaftaran foto wajah pertama kali',
                        'time' => 'Penting',
                    ];
                }
            }

            $view->with('employeeNotifications', $notifications);
        });
    }
}
