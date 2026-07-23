<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = User::where('nip', 'EMP2026001')->first();
        $geofence = GeofenceSetting::first();

        if ($employee && $geofence) {
            AttendanceLog::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'date' => '2026-07-22',
                    'type' => 'MASUK',
                ],
                [
                    'geofence_id' => $geofence->id,
                    'time' => '07:54:12',
                    'status' => 'TEPAT_WAKTU',
                    'lat' => -6.20875000,
                    'lng' => 106.84562000,
                    'distance_from_office' => 12,
                    'face_confidence_score' => 98.50,
                    'photo_path' => 'assets/images/avatar/avatar.jpg',
                    'notes' => 'Presensi via Mobile Web',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                ]
            );

            AttendanceLog::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'date' => '2026-07-22',
                    'type' => 'PULANG',
                ],
                [
                    'geofence_id' => $geofence->id,
                    'time' => '17:05:30',
                    'status' => 'TEPAT_WAKTU',
                    'lat' => -6.20875000,
                    'lng' => 106.84562000,
                    'distance_from_office' => 12,
                    'face_confidence_score' => 98.50,
                    'photo_path' => 'assets/images/avatar/avatar.jpg',
                    'notes' => 'Absen pulang normal',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                ]
            );

            AttendanceLog::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'date' => '2026-07-21',
                    'type' => 'MASUK',
                ],
                [
                    'geofence_id' => $geofence->id,
                    'time' => '08:18:05',
                    'status' => 'TERLAMBAT',
                    'lat' => -6.20870000,
                    'lng' => 106.84560000,
                    'distance_from_office' => 28,
                    'face_confidence_score' => 96.20,
                    'photo_path' => 'assets/images/avatar/avatar.jpg',
                    'notes' => 'Macet lalu lintas',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                ]
            );
        }
    }
}
