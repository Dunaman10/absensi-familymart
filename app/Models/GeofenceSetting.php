<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeofenceSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'geofence_settings';

    protected $fillable = [
        'office_name',
        'lat',
        'lng',
        'radius_in_meters',
        'shift_start',
        'shift_end',
        'late_tolerance_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'radius_in_meters' => 'integer',
            'late_tolerance_minutes' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship to AttendanceLogs.
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'geofence_id');
    }
}
