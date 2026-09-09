<?php

namespace Database\Seeders;

use App\Models\LeaveSetting;
use Illuminate\Database\Seeder;

class LeaveSettingSeeder extends Seeder
{
    /**
     * Seed the leave settings with default config.
     */
    public function run(): void
    {
        LeaveSetting::updateOrCreate(
            ['id' => 1],
            ['max_annual_leave_days' => 12]
        );
    }
}
