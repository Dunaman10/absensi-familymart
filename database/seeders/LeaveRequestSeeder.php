<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Seed realistic leave request data using employees from the database.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean truncation
        Schema::disableForeignKeyConstraints();
        LeaveRequest::truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::where('role', 'admin')->first();
        $employees = User::where('role', 'user')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employee users found. Skipping LeaveRequestSeeder.');

            return;
        }

        // 1. Seed specific data for Syarif Hidayatullah (User ID 2)
        $syarif = User::where('name', 'LIKE', '%Syarif%')->first() ?? $employees->first();

        // Request 1: 1 day Cuti (Approved) -> uses 1 day of quota (remaining 11)
        LeaveRequest::create([
            'user_id' => $syarif->id,
            'type' => 'CUTI',
            'start_date' => '2026-09-02',
            'end_date' => '2026-09-02',
            'duration_days' => 1,
            'reason' => 'Cuti tahunan untuk keperluan acara keluarga',
            'status' => 'APPROVED',
            'reviewed_by' => $admin?->id,
            'reviewed_at' => '2026-09-01 14:00:00',
            'created_at' => '2026-09-01 08:30:00',
            'updated_at' => '2026-09-01 14:00:00',
        ]);

        // Request 2: 1 day Izin (Rejected)
        LeaveRequest::create([
            'user_id' => $syarif->id,
            'type' => 'IZIN',
            'start_date' => '2026-09-05',
            'end_date' => '2026-09-05',
            'duration_days' => 1,
            'reason' => 'Izin mengurus perpanjangan SIM di Samsat keliling',
            'status' => 'REJECTED',
            'reviewed_by' => $admin?->id,
            'reviewed_at' => '2026-09-04 15:30:00',
            'reject_reason' => 'Jadwal bertepatan dengan audit toko dan stok opname bulanan',
            'created_at' => '2026-09-04 09:00:00',
            'updated_at' => '2026-09-04 15:30:00',
        ]);

        // 2. Seed realistic data for other employees
        $otherEmployees = $employees->where('id', '!=', $syarif->id);

        $presets = [
            [
                'type' => 'CUTI',
                'days' => 2,
                'start' => '2026-09-12',
                'end' => '2026-09-13',
                'status' => 'PENDING',
                'reason' => 'Cuti menghadiri pernikahan adik kandung di Bandung',
                'attach' => null,
            ],
            [
                'type' => 'SAKIT',
                'days' => 2,
                'start' => '2026-09-08',
                'end' => '2026-09-09',
                'status' => 'APPROVED',
                'reason' => 'Demam tinggi dan flu berat, istirahat atas saran dokter',
                'attach' => 'uploads/leaves/surat_dokter_sample.pdf',
            ],
            [
                'type' => 'IZIN',
                'days' => 1,
                'start' => '2026-09-10',
                'end' => '2026-09-10',
                'status' => 'PENDING',
                'reason' => 'Antar orang tua kontrol kesehatan di RS Hermina',
                'attach' => null,
            ],
            [
                'type' => 'CUTI',
                'days' => 3,
                'start' => '2026-09-15',
                'end' => '2026-09-17',
                'status' => 'PENDING',
                'reason' => 'Cuti tahunan yang sudah direncanakan',
                'attach' => null,
            ],
            [
                'type' => 'SAKIT',
                'days' => 3,
                'start' => '2026-08-25',
                'end' => '2026-08-27',
                'status' => 'APPROVED',
                'reason' => 'Rawat jalan diagnosa tifus, surat RS terlampir',
                'attach' => 'uploads/leaves/surat_dokter_sample.pdf',
            ],
            [
                'type' => 'IZIN',
                'days' => 1,
                'start' => '2026-08-20',
                'end' => '2026-08-20',
                'status' => 'REJECTED',
                'reason' => 'Keperluan pribadi mendadak',
                'reject' => 'Alasan kurang spesifik dan tidak melampirkan berkas pendukung',
                'attach' => null,
            ],
            [
                'type' => 'CUTI',
                'days' => 2,
                'start' => '2026-08-15',
                'end' => '2026-08-16',
                'status' => 'APPROVED',
                'reason' => 'Cuti tahunan silaturahmi keluarga',
                'attach' => null,
            ],
            [
                'type' => 'IZIN',
                'days' => 1,
                'start' => '2026-09-09',
                'end' => '2026-09-09',
                'status' => 'APPROVED',
                'reason' => 'Mengurus administrasi kependudukan di Dukcapil',
                'attach' => null,
            ],
        ];

        foreach ($otherEmployees->take(6) as $index => $emp) {
            $preset1 = $presets[$index % count($presets)];
            $preset2 = $presets[($index + 3) % count($presets)];

            foreach ([$preset1, $preset2] as $p) {
                LeaveRequest::create([
                    'user_id' => $emp->id,
                    'type' => $p['type'],
                    'start_date' => $p['start'],
                    'end_date' => $p['end'],
                    'duration_days' => $p['days'],
                    'reason' => $p['reason'],
                    'attachment_path' => $p['attach'] ?? null,
                    'status' => $p['status'],
                    'reviewed_by' => $p['status'] !== 'PENDING' ? $admin?->id : null,
                    'reviewed_at' => $p['status'] !== 'PENDING' ? '2026-09-01 10:00:00' : null,
                    'reject_reason' => $p['reject'] ?? null,
                    'created_at' => '2026-09-01 09:00:00',
                    'updated_at' => '2026-09-01 10:00:00',
                ]);
            }
        }

        $this->command->info('LeaveRequestSeeder: '.LeaveRequest::count().' leave requests seeded successfully.');
    }
}
