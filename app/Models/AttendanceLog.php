<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'user_id',
        'geofence_id',
        'date',
        'time',
        'type',
        'status',
        'approval_status',
        'approval_notes',
        'approved_by',
        'lat',
        'lng',
        'distance_from_office',
        'is_outside_radius',
        'face_confidence_score',
        'photo_path',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'lat' => 'float',
            'lng' => 'float',
            'distance_from_office' => 'integer',
            'is_outside_radius' => 'boolean',
            'face_confidence_score' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to GeofenceSetting.
     */
    public function geofence(): BelongsTo
    {
        return $this->belongsTo(GeofenceSetting::class, 'geofence_id');
    }

    /**
     * Relationship to Approver User.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
