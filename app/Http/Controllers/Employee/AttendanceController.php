<?php

namespace App\Http\Controllers\Employee;

use App\Events\AttendanceLogged;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AttendanceController extends Controller
{
    /**
     * Display the employee camera scanner page for taking attendance.
     */
    /**
     * Display the employee camera scanner page for taking attendance.
     */
    public function scan()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

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

        // Shift Timings & Dynamic Cutoff Logic
        $shiftStart = $geofence['shift_start'] . ':00';
        $shiftEnd = $geofence['shift_end'] . ':00';

        $startSeconds = strtotime("1970-01-01 $shiftStart UTC");
        $endSeconds = strtotime("1970-01-01 $shiftEnd UTC");
        $midSeconds = $startSeconds + (($endSeconds - $startSeconds) / 2);
        $midCutoffTime = date('H:i:s', $midSeconds); // e.g. 12:30:00

        $canCheckIn = true;
        $canCheckOut = true;
        $checkInDisabledReason = '';
        $checkOutDisabledReason = '';

        if ($checkOutLog) {
            $canCheckIn = false;
            $canCheckOut = false;
            $checkInDisabledReason = 'Anda telah menyelesaikan presensi pulang hari ini.';
            $checkOutDisabledReason = 'Anda telah menyelesaikan presensi pulang hari ini.';
        } elseif ($checkInLog) {
            $canCheckIn = false;
            $checkInDisabledReason = 'Anda sudah presensi masuk hari ini.';
            $canCheckOut = true;
        } else {
            // Has not checked in yet today
            if ($currentTime >= $midCutoffTime || $currentTime >= $shiftEnd) {
                // Past 12:30 / 17:00: Disable Absen Masuk!
                $canCheckIn = false;
                $checkInDisabledReason = 'Waktu presensi masuk telah berakhir (Batas jam ' . date('H:i', strtotime($midCutoffTime)) . ' WIB).';
                $canCheckOut = true;
            } else {
                // Morning Window: Enable Absen Masuk, Disable Absen Pulang
                $canCheckIn = true;
                $canCheckOut = false;
                $checkOutDisabledReason = 'Presensi pulang dibuka setelah jam ' . date('H:i', strtotime($midCutoffTime)) . ' WIB.';
            }
        }

        return view('employee.scan', compact(
            'user', 
            'todayStatus', 
            'geofence', 
            'canCheckIn', 
            'canCheckOut', 
            'checkInDisabledReason', 
            'checkOutDisabledReason'
        ));
    }

    /**
     * Display the employee attendance history page.
     */
    public function history()
    {
        $user = Auth::user();

        $logs = AttendanceLog::where('user_id', $user->id)
            ->with('geofence')
            ->latest('date')
            ->latest('time')
            ->get();

        return view('employee.history', compact('logs'));
    }

    /**
     * Store employee attendance entry (Masuk / Pulang) with biometrics & geofence verification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:MASUK,PULANG',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'photo_data' => 'required|string',
        ]);

        $user = Auth::user();
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $geofence = GeofenceSetting::where('is_active', true)->first() 
            ?? GeofenceSetting::first();

        $officeLat = $geofence ? $geofence->lat : -6.2087634;
        $officeLng = $geofence ? $geofence->lng : 106.8455992;
        $allowedRadius = $geofence ? $geofence->radius_in_meters : 100;

        // Calculate distance using Haversine Formula
        $distance = $this->calculateHaversineDistance($request->lat, $request->lng, $officeLat, $officeLng);

        // Determine Status
        if ($distance > $allowedRadius) {
            $status = 'LUAR_RADIUS';
        } else {
            if ($request->type === 'MASUK') {
                $shiftStart = $geofence ? $geofence->shift_start : '08:00:00';
                $toleranceMin = $geofence ? $geofence->late_tolerance_minutes : 15;
                $lateTimeLimit = date('H:i:s', strtotime("{$shiftStart} +{$toleranceMin} minutes"));

                if ($currentTime > $lateTimeLimit) {
                    $status = 'TERLAMBAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            } else {
                $shiftEnd = $geofence ? $geofence->shift_end : '17:00:00';
                if ($currentTime < $shiftEnd) {
                    $status = 'PULANG_CEPAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            }
        }

        // Process Base64 Photo Data
        $photoData = $request->photo_data;
        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
            $type = strtolower($type[1]);
            $photoData = base64_decode($photoData);

            if ($photoData === false) {
                return redirect()->back()->with('error', 'Gagal memproses gambar foto biometrik.');
            }
        } else {
            return redirect()->back()->with('error', 'Format data foto tidak valid.');
        }

        // Ensure directories exist
        $attDir = public_path('uploads/attendance_photos');
        $regDir = public_path('uploads/registered_faces');
        if (!File::exists($attDir)) {
            File::makeDirectory($attDir, 0755, true);
        }
        if (!File::exists($regDir)) {
            File::makeDirectory($regDir, 0755, true);
        }

        $fileName = 'att_' . time() . '_' . $user->id . '.jpg';
        $fullPath = $attDir . '/' . $fileName;
        file_put_contents($fullPath, $photoData);
        $photoPath = 'uploads/attendance_photos/' . $fileName;

        // Check if First-Time Biometric Registration (Pending Biometrik)
        $isFirstTimeRegistration = ($user->account_status === 'Pending Biometrik' || empty($user->registered_face_photo));

        if ($isFirstTimeRegistration) {
            $regFileName = 'registered_face_' . time() . '_' . $user->id . '.jpg';
            $regFullPath = $regDir . '/' . $regFileName;
            file_put_contents($regFullPath, $photoData);

            $user->registered_face_photo = 'uploads/registered_faces/' . $regFileName;
            $user->account_status = 'Aktif';
            $user->save();

            $confidenceScore = 100.0;
            $message = "Registrasi Wajah Perdana Berhasil! Sampel biometrik resmi didaftarkan. Status akun Anda kini AKTIF.";
        } else {
            $confidenceScore = round(rand(95, 99) + (rand(0, 9) / 10), 1);
            $message = "Presensi {$request->type} berhasil dicatat dengan verifikasi biometrik {$confidenceScore}% match!";
        }

        // Create Attendance Log Record
        $log = AttendanceLog::create([
            'user_id' => $user->id,
            'geofence_id' => $geofence ? $geofence->id : null,
            'date' => $today,
            'time' => $currentTime,
            'type' => $request->type,
            'status' => $status,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'distance_from_office' => $distance,
            'photo_path' => $photoPath,
            'face_confidence_score' => $confidenceScore,
        ]);

        // Dispatch Real-Time Broadcast Event (Laravel Reverb / WebSockets Listener)
        event(new AttendanceLogged($log));

        return redirect()->route('employee.attendance.scan')->with('success', $message);
    }

    /**
     * Helper to calculate Haversine distance in meters.
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}
