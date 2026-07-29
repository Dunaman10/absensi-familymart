<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account
        User::updateOrCreate(
            ['nip' => 'ADMIN001'],
            [
                'name' => 'Admin Hasan',
                'email' => 'admin@absensiku.com',
                'position' => 'HR Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'account_status' => 'Aktif',
                'face_descriptor' => null,
                'registered_face_photo' => null,
            ]
        );

        // 15 Karyawan Dummy
        $employees = [
            [
                'nip' => 'EMP2026001',
                'name' => 'Syarif Hidayatullah',
                'email' => 'syarif@absensiku.com',
                'position' => 'Software Engineer',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => 'assets/images/avatar/avatar.jpg',
            ],
            [
                'nip' => 'EMP2026002',
                'name' => 'Putri Zahwa Rahmadhani',
                'email' => 'putri@absensiku.com',
                'position' => 'UI/UX Designer',
                'role' => 'user',
                'account_status' => 'Pending Biometrik',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026003',
                'name' => 'Budi Santoso',
                'email' => 'budi@absensiku.com',
                'position' => 'Backend Developer',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026004',
                'name' => 'Siti Rahmawati',
                'email' => 'siti@absensiku.com',
                'position' => 'HR Specialist',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026005',
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@absensiku.com',
                'position' => 'Frontend Developer',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026006',
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@absensiku.com',
                'position' => 'QA Engineer',
                'role' => 'user',
                'account_status' => 'Pending Biometrik',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026007',
                'name' => 'Fajar Nugraha',
                'email' => 'fajar.nugraha@absensiku.com',
                'position' => 'DevOps Engineer',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026008',
                'name' => 'Gita Gutawa',
                'email' => 'gita.gutawa@absensiku.com',
                'position' => 'Product Manager',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026009',
                'name' => 'Hadi Sucipto',
                'email' => 'hadi.sucipto@absensiku.com',
                'position' => 'Data Analyst',
                'role' => 'user',
                'account_status' => 'Non-Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026010',
                'name' => 'Indah Permata',
                'email' => 'indah.permata@absensiku.com',
                'position' => 'Digital Marketing',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026011',
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@absensiku.com',
                'position' => 'Accountant',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026012',
                'name' => 'Kurniawan Prasetyo',
                'email' => 'kurniawan@absensiku.com',
                'position' => 'Mobile Developer',
                'role' => 'user',
                'account_status' => 'Pending Biometrik',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026013',
                'name' => 'Lani Wijaya',
                'email' => 'lani.wijaya@absensiku.com',
                'position' => 'System Administrator',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026014',
                'name' => 'Muhammad Iqbal',
                'email' => 'm.iqbal@absensiku.com',
                'position' => 'Cyber Security Analyst',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
            [
                'nip' => 'EMP2026015',
                'name' => 'Nadia Kurnia',
                'email' => 'nadia.kurnia@absensiku.com',
                'position' => 'Technical Writer',
                'role' => 'user',
                'account_status' => 'Aktif',
                'registered_face_photo' => null,
            ],
        ];

        $defaultPassword = Hash::make('password');

        foreach ($employees as $emp) {
            User::updateOrCreate(
                ['nip' => $emp['nip']],
                [
                    'name' => $emp['name'],
                    'email' => $emp['email'],
                    'position' => $emp['position'],
                    'password' => $defaultPassword,
                    'role' => $emp['role'],
                    'account_status' => $emp['account_status'],
                    'face_descriptor' => null,
                    'registered_face_photo' => $emp['registered_face_photo'],
                ]
            );
        }
    }
}
