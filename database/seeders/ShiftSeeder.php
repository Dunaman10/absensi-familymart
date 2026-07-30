<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::updateOrCreate(
            ['name' => 'Shift Pagi (Family Mart)'],
            [
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'late_tolerance_minutes' => 15,
                'description' => 'Shift pagi operasional gerai Family Mart (06.00 WIB - 14.00 WIB)',
            ]
        );

        Shift::updateOrCreate(
            ['name' => 'Shift Siang (Family Mart)'],
            [
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'late_tolerance_minutes' => 15,
                'description' => 'Shift siang operasional gerai Family Mart (14.00 WIB - 22.00 WIB)',
            ]
        );

        Shift::updateOrCreate(
            ['name' => 'Shift Malam (Family Mart 24 Jam)'],
            [
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'late_tolerance_minutes' => 15,
                'description' => 'Shift malam operasional gerai 24 jam Family Mart (22.00 WIB - 06.00 WIB)',
            ]
        );
    }
}
