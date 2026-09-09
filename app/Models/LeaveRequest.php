<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'duration_days',
        'reason',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship to the employee who submitted the leave request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the admin who reviewed the leave request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope: only pending requests.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope: only approved requests.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Scope: only rejected requests.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'REJECTED');
    }

    /**
     * Scope: currently active leaves (approved and today falls within date range).
     */
    public function scopeActiveToday(Builder $query): Builder
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        return $query->where('status', 'APPROVED')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }
}
