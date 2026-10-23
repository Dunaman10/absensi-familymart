<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\GeofenceSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use ZipArchive;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds for 15 employees attendance trials from XLSX.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/Data_Absensi_15_Karyawan.xlsx');

        if (! file_exists($filePath)) {
            $this->command?->error("File Excel tidak ditemukan pada path: {$filePath}");

            return;
        }

        $rows = $this->parseXlsx($filePath);

        if (count($rows) <= 1) {
            $this->command?->error('File Excel kosong atau format tidak sesuai.');

            return;
        }

        // Header berada di baris pertama
        $dataRows = array_slice($rows, 1);
        $totalRows = count($dataRows);
        $this->command?->info("Membaca {$totalRows} baris data percobaan dari Data_Absensi_15_Karyawan.xlsx...");

        $admin = User::where('role', 'admin')->first();
        $geofence = GeofenceSetting::where('is_active', true)->first() ?? GeofenceSetting::first();

        // Fallback geofence jika belum ada setting sama sekali
        if (! $geofence) {
            $geofence = GeofenceSetting::create([
                'office_name' => 'Kantor Pusat - FamilyMart Head Office',
                'lat' => -6.20880000,
                'lng' => 106.84560000,
                'radius_in_meters' => 50,
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
            ]);
        }

        $seededCount = 0;

        foreach ($dataRows as $row) {
            $no = (int) ($row['A'] ?? 0);
            $idKaryawan = (int) ($row['B'] ?? 1);
            $nip = trim($row['C'] ?? '');
            $namaKaryawan = trim($row['D'] ?? "Karyawan {$idKaryawan}");
            $percobaan = (int) ($row['E'] ?? 1);
            $skenario = trim($row['F'] ?? 'SAMA (Wajah Sendiri)');
            $gpsCoords = trim($row['G'] ?? '-6.20885, 106.84562');
            $distanceM = (float) ($row['H'] ?? 0);
            $statusRadius = trim($row['I'] ?? 'Dalam Radius');
            $skorWajah = (float) ($row['J'] ?? 90.0);
            $jarakEuclidean = (float) ($row['K'] ?? 0.05);
            $statusVerifikasi = trim($row['L'] ?? 'Cocok (Diterima)');
            $statusKehadiran = trim($row['M'] ?? 'DISETUJUI (Tepat Waktu)');

            if (empty($nip)) {
                continue;
            }

            // Pastikan user karyawan sudah ada di database
            $user = User::where('nip', $nip)->first();
            if (! $user) {
                $user = User::create([
                    'nip' => $nip,
                    'name' => $namaKaryawan,
                    'email' => strtolower(str_replace(' ', '', $nip)).'@absensiku.com',
                    'position' => 'Store Crew',
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'account_status' => 'Aktif',
                    'registered_face_photo' => "assets/images/avatar/avatar-{$idKaryawan}.jpg",
                ]);
            }

            // Parsing koordinat GPS (lat, lng)
            $coords = explode(',', $gpsCoords);
            $lat = isset($coords[0]) ? (float) trim($coords[0]) : -6.20885000;
            $lng = isset($coords[1]) ? (float) trim($coords[1]) : 106.84562000;

            $isOutsideRadius = ($statusRadius === 'Luar Radius');
            $distanceFromOffice = (int) round($distanceM);

            // Tentukan status dan approval status berdasarkan skenario uji coba
            if (str_contains($statusKehadiran, 'DISETUJUI')) {
                $status = 'TEPAT_WAKTU';
                $approvalStatus = 'APPROVED';
                $approvalNotes = 'Presensi disetujui otomatis oleh sistem (Wajah terverifikasi dan berada dalam radius kantor).';
                $approvedBy = $admin?->id;
            } elseif (str_contains($statusKehadiran, 'PENDING')) {
                $status = 'LUAR_RADIUS';
                $approvalStatus = 'PENDING';
                $approvalNotes = "Presensi terdeteksi di luar radius gerai ({$distanceFromOffice} m). Menunggu konfirmasi admin.";
                $approvedBy = null;
            } else {
                // GAGAL (Wajah Tidak Cocok / Spoof)
                $status = $isOutsideRadius ? 'LUAR_RADIUS' : 'TERLAMBAT';
                $approvalStatus = 'REJECTED';
                $approvalNotes = "DITOLAK OTOMATIS: Verifikasi biometrik gagal (Skor {$skorWajah}%, Euclidean {$jarakEuclidean}). Terindikasi bukan wajah terdaftar / spoofing.";
                $approvedBy = $admin?->id;
            }

            // Tanggal simulasi: 10 hari berturut-turut (misal: 13 Oktober 2026 s/d 22 Oktober 2026)
            $date = Carbon::create(2026, 10, 13)->addDays($percobaan - 1)->toDateString();

            // Variasi jam presensi masuk yang realistis
            if ($approvalStatus === 'APPROVED') {
                $minute = 45 + ($percobaan % 12);
                $second = 10 + (($idKaryawan * 7 + $percobaan) % 48);
                $time = sprintf('07:%02d:%02d', $minute, $second);
            } elseif ($approvalStatus === 'PENDING') {
                $minute = 5 + ($percobaan % 10);
                $second = 12 + (($idKaryawan * 3) % 45);
                $time = sprintf('08:%02d:%02d', $minute, $second);
            } else {
                $minute = 15 + ($percobaan % 15);
                $second = 20 + (($idKaryawan * 5) % 38);
                $time = sprintf('08:%02d:%02d', $minute, $second);
            }

            // Path avatar foto presensi
            if (str_contains($skenario, 'BEDA')) {
                $otherNum = (($idKaryawan + 5) % 21) + 1;
                $photoPath = file_exists(public_path("assets/images/avatar/avatar-{$otherNum}.jpg"))
                    ? "assets/images/avatar/avatar-{$otherNum}.jpg"
                    : 'assets/images/avatar/avatar-fallback.jpg';
            } else {
                $avatarNum = (($idKaryawan - 1) % 21) + 1;
                $photoPath = file_exists(public_path("assets/images/avatar/avatar-{$avatarNum}.jpg"))
                    ? "assets/images/avatar/avatar-{$avatarNum}.jpg"
                    : 'assets/images/avatar/avatar-fallback.jpg';
            }

            $notes = "Percobaan #{$percobaan} [{$skenario}] | Verifikasi: {$statusVerifikasi} | Euclidean: {$jarakEuclidean} | Jarak: {$distanceFromOffice}m | Status: {$statusKehadiran}";

            // Simpan / Perbarui data presensi secara idempotent
            AttendanceLog::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $date,
                    'type' => 'MASUK',
                ],
                [
                    'geofence_id' => $geofence?->id,
                    'time' => $time,
                    'status' => $status,
                    'approval_status' => $approvalStatus,
                    'approval_notes' => $approvalNotes,
                    'approved_by' => $approvedBy,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance_from_office' => $distanceFromOffice,
                    'is_outside_radius' => $isOutsideRadius,
                    'face_confidence_score' => $skorWajah,
                    'photo_path' => $photoPath,
                    'notes' => $notes,
                    'ip_address' => '192.168.1.'.(10 + $idKaryawan),
                    'user_agent' => 'Mozilla/5.0 (Linux; Android 14; Mobile) AppleWebKit/537.36 Chrome/128.0.0.0 Mobile Safari/537.36',
                ]
            );

            $seededCount++;
        }

        $this->command?->info("Sukses! Berhasil melakukan seed sebanyak {$seededCount} data log presensi dari file Excel.");
    }

    /**
     * Parsing file XLSX mandiri tanpa ketergantungan library pihak ketiga.
     *
     * @return array<int, array<string, string>>
     */
    private function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // Baca shared strings jika tersedia di dalam package XLSX
        $sharedStrings = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $stringsXml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
            if ($stringsXml) {
                foreach ($stringsXml->si as $si) {
                    $sharedStrings[] = (string) ($si->t ?? '');
                }
            }
        }

        // Baca worksheet sheet1.xml
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (! $sheetXmlContent) {
            return [];
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $r) {
            $row = [];
            foreach ($r->c as $c) {
                $col = preg_replace('/[0-9]/', '', (string) $c['r']);
                $t = (string) $c['t'];

                if ($t === 'inlineStr' && isset($c->is->t)) {
                    $v = (string) $c->is->t;
                } elseif ($t === 's' && isset($sharedStrings[(int) $c->v])) {
                    $v = $sharedStrings[(int) $c->v];
                } else {
                    $v = (string) $c->v;
                }

                $row[$col] = trim((string) $v);
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
