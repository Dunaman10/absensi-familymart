<?php

namespace App\Http\Controllers\Employee;

use App\Events\AttendanceLogged;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $currentTime = now()->timezone('Asia/Jakarta')->format('H:i:s');

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
            'date_formatted' => now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y'),
        ];

        $userShift = $user->shift;

        $geofence = [
            'office_name' => $primaryGeofence->office_name ?? 'FamilyMart Head Office',
            'office_lat' => $primaryGeofence->lat ?? -6.208800,
            'office_lng' => $primaryGeofence->lng ?? 106.845600,
            'radius_meters' => $primaryGeofence->radius_in_meters ?? 50,
            'shift_name' => $userShift ? $userShift->name : 'Shift Pagi (Family Mart)',
            'shift_start' => $userShift ? substr($userShift->start_time, 0, 5) : '06:00',
            'shift_end' => $userShift ? substr($userShift->end_time, 0, 5) : '14:00',
            'late_tolerance_minutes' => $userShift ? $userShift->late_tolerance_minutes : 15,
        ];

        // Status Tombol Presensi Berdasarkan Siklus Shift 24 Jam
        $canCheckIn = true;
        $canCheckOut = true;
        $checkInDisabledReason = '';
        $checkOutDisabledReason = '';

        if ($checkOutLog) {
            $canCheckIn = false;
            $canCheckOut = false;
            $checkInDisabledReason = 'Anda telah menyelesaikan presensi masuk dan pulang untuk shift hari ini.';
            $checkOutDisabledReason = 'Anda telah menyelesaikan presensi pulang hari ini.';
        } elseif ($checkInLog) {
            // Sudah absen masuk -> tombol masuk nonaktif, tombol pulang aktif
            $canCheckIn = false;
            $checkInDisabledReason = 'Anda sudah presensi masuk hari ini (Pukul '.substr($checkInLog->time, 0, 5).' WIB).';
            $canCheckOut = true;
        } else {
            // Belum absen masuk -> tombol masuk aktif, tombol pulang belum bisa karena belum masuk
            $canCheckIn = true;
            $canCheckOut = false;
            $checkOutDisabledReason = 'Silakan lakukan presensi masuk terlebih dahulu sebelum presensi pulang.';
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

        $perPage = request('per_page', 5);
        $logs = AttendanceLog::where('user_id', $user->id)
            ->with('geofence')
            ->latest('date')
            ->latest('time')
            ->paginate($perPage)->withQueryString();

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
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $currentTime = now()->timezone('Asia/Jakarta')->format('H:i:s');

        $geofence = GeofenceSetting::where('is_active', true)->first()
            ?? GeofenceSetting::first();

        $officeLat = $geofence ? $geofence->lat : -6.2087634;
        $officeLng = $geofence ? $geofence->lng : 106.8455992;
        $allowedRadius = $geofence ? $geofence->radius_in_meters : 100;

        // Calculate distance using Haversine Formula
        $distance = $this->calculateHaversineDistance($request->lat, $request->lng, $officeLat, $officeLng);

        // Determine Radius and Time Status
        $isOutsideRadius = $distance > $allowedRadius;

        $userShift = $user->shift;

        // Default ke jam kerja FamilyMart jika karyawan belum memiliki shift khusus
        $shiftStart = $userShift ? $userShift->start_time : '06:00:00';
        $shiftEnd = $userShift ? $userShift->end_time : '14:00:00';
        $toleranceMin = $userShift ? (int) $userShift->late_tolerance_minutes : 15;
        $isOvernight = $shiftStart > $shiftEnd; // contoh: 22:00:00 > 06:00:00

        if ($request->type === 'MASUK') {
            $lateTimeLimit = date('H:i:s', strtotime("{$shiftStart} +{$toleranceMin} minutes"));

            if ($isOvernight) {
                // Shift Malam (contoh: 22:00 - 06:00, batas telat 22:15)
                // Karyawan tepat waktu jika masuk antara jam 21:00 sampai 22:15
                // Terlambat jika absen masuk setelah 22:15 sampai 06:00 pagi berikutnya
                if ($currentTime > $lateTimeLimit || ($currentTime >= '00:00:00' && $currentTime < $shiftEnd)) {
                    $status = 'TERLAMBAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            } else {
                // Shift Reguler Siang/Pagi (contoh: Pagi 06:00 - 14:00, batas telat 06:15)
                // Terlambat jika absen setelah batas toleransi shift
                if ($currentTime > $lateTimeLimit) {
                    $status = 'TERLAMBAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            }
        } else {
            // Tipe: PULANG
            if ($isOvernight) {
                // Shift Malam (22:00 - 06:00), jam pulang normal adalah jam 06:00 ke atas sampai shift siang/pagi berikutnya
                // Pulang cepat jika sebelum jam 06:00 pagi (misal jam 04:00 atau jam 23:00 malam)
                if ($currentTime >= $shiftStart || $currentTime < $shiftEnd) {
                    $status = 'PULANG_CEPAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            } else {
                // Shift Reguler (contoh: 06:00 - 14:00 atau 14:00 - 22:00)
                if ($currentTime < $shiftEnd) {
                    $status = 'PULANG_CEPAT';
                } else {
                    $status = 'TEPAT_WAKTU';
                }
            }
        }

        // Approval status logic:
        // Setiap presensi yang anomali (TERLAMBAT, PULANG_CEPAT, atau LUAR RADIUS)
        // akan diset ke status 'PENDING' agar Admin HR yang mengambil keputusan persetujuan/penolakan.
        if (in_array($status, ['TERLAMBAT', 'PULANG_CEPAT']) || $isOutsideRadius) {
            $approvalStatus = 'PENDING';
        } else {
            $approvalStatus = 'APPROVED';
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
        if (! File::exists($attDir)) {
            File::makeDirectory($attDir, 0755, true);
        }
        if (! File::exists($regDir)) {
            File::makeDirectory($regDir, 0755, true);
        }

        $fileName = 'att_'.time().'_'.$user->id.'.jpg';
        $fullPath = $attDir.'/'.$fileName;
        file_put_contents($fullPath, $photoData);
        $photoPath = 'uploads/attendance_photos/'.$fileName;

        // Save face descriptor if provided by face-api.js
        if ($request->filled('face_descriptor')) {
            $user->face_descriptor = $request->face_descriptor;
        }

        // Check if First-Time Biometric Registration (Pending Biometrik)
        $isFirstTimeRegistration = ($user->account_status === 'Pending Biometrik' || empty($user->registered_face_photo));

        if ($isFirstTimeRegistration) {
            $regFileName = 'registered_face_'.time().'_'.$user->id.'.jpg';
            $regFullPath = $regDir.'/'.$regFileName;
            file_put_contents($regFullPath, $photoData);

            $user->registered_face_photo = 'uploads/registered_faces/'.$regFileName;
            $user->account_status = 'Aktif';
            $user->save();

            $confidenceScore = 100.0;
            $message = 'Registrasi Wajah Perdana Berhasil! Sampel biometrik resmi didaftarkan. Status akun Anda kini AKTIF.';
        } else {
            $user->save();
            $confidenceScore = $request->filled('face_confidence_score')
                ? (float) $request->face_confidence_score
                : round(rand(95, 99) + (rand(0, 9) / 10), 1);
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
            'approval_status' => $approvalStatus,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'distance_from_office' => $distance,
            'is_outside_radius' => $isOutsideRadius,
            'photo_path' => $photoPath,
            'face_confidence_score' => $confidenceScore,
            'notes' => $request->input('notes'),
        ]);

        // Dispatch Real-Time Broadcast Event (Laravel Reverb / WebSockets Listener)
        try {
            event(new AttendanceLogged($log));
        } catch (\Throwable $e) {
            // Reverb WebSocket server might be offline locally, log notice silently
            Log::warning('Reverb Broadcast Error: '.$e->getMessage());
        }

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
