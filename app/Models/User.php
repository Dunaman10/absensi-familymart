<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nip',
        'name',
        'email',
        'position',
        'shift_id',
        'password',
        'role',
        'account_status',
        'face_descriptor',
        'registered_face_photo',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship to Shift.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relationship to AttendanceLogs.
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Regular Employee.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Generate next NIP automatically for employees (Format: EMP{YEAR}{SEQ}, e.g. EMP2026001).
     */
    public static function generateNextNip(): string
    {
        $year = date('Y');
        $prefix = "EMP{$year}";

        $latestUser = self::where('role', 'user')
            ->where('nip', 'LIKE', "{$prefix}%")
            ->orderBy('nip', 'desc')
            ->first();

        if ($latestUser && preg_match('/'.$prefix.'(\d+)/', $latestUser->nip, $matches)) {
            $nextNum = (int) $matches[1] + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix.str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}
