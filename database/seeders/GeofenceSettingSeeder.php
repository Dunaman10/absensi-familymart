<?php

namespace Database\Seeders;

use App\Models\GeofenceSetting;
use Illuminate\Database\Seeder;

class GeofenceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeofenceSetting::updateOrCreate(
            ['office_name' => 'Kantor Pusat - Head Office Jakarta'],
            [
                'lat' => -6.20880000,
                'lng' => 106.84560000,
                'radius_in_meters' => 50,
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
            ]
        );

        GeofenceSetting::updateOrCreate(
            ['office_name' => 'Cabang Bandung - Tech Park'],
            [
                'lat' => -6.91746390,
                'lng' => 107.61912280,
                'radius_in_meters' => 100,
                'shift_start' => '08:30:00',
                'shift_end' => '17:30:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
            ]
        );
    }
}
