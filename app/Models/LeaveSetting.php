<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    protected $table = 'leave_settings';

    protected $fillable = [
        'max_annual_leave_days',
    ];

    protected function casts(): array
    {
        return [
            'max_annual_leave_days' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the max annual leave days from the single-row config.
     */
    public static function getMaxDays(): int
    {
        return static::first()?->max_annual_leave_days ?? 12;
    }
}
