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
                'position' => 'HR Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'account_status' => 'Aktif',
                'face_descriptor' => null,
                'registered_face_photo' => null,
            ]
        );

        // Employee Accounts
        User::updateOrCreate(
            ['nip' => 'EMP2026001'],
            [
                'name' => 'Syarif Hidayatullah',
                'position' => 'Software Engineer',
                'password' => Hash::make('password'),
                'role' => 'user',
                'account_status' => 'Aktif',
                'face_descriptor' => null,
                'registered_face_photo' => 'assets/images/avatar/avatar.jpg',
            ]
        );

        User::updateOrCreate(
            ['nip' => 'EMP2026002'],
            [
                'name' => 'Budi Santoso',
                'position' => 'UI/UX Designer',
                'password' => Hash::make('password'),
                'role' => 'user',
                'account_status' => 'Aktif',
                'face_descriptor' => null,
                'registered_face_photo' => null,
            ]
        );

        User::updateOrCreate(
            ['nip' => 'EMP2026003'],
            [
                'name' => 'Siti Rahma',
                'position' => 'HR Specialist',
                'password' => Hash::make('password'),
                'role' => 'user',
                'account_status' => 'Pending Biometrik',
                'face_descriptor' => null,
                'registered_face_photo' => null,
            ]
        );
    }
}
