<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'scheduled_date',
        'scheduled_time',
        'duration_minutes',
        'started_at',
        'reviewed_at',
        'completed_at',

        // ✅ recurrence fields (BẮT BUỘC)
        'is_template',
        'series_id',
        'recurrence',
        'recurrence_until',
        'last_generated_for',
    ];

    protected $casts = [
        'scheduled_date'      => 'date',
        'recurrence_until'    => 'date',
        'last_generated_for'  => 'date',
        'is_template'         => 'boolean',
        'started_at'          => 'datetime',
        'reviewed_at'         => 'datetime',
        'completed_at'        => 'datetime',
    ];

    public const STATUS_TODO     = 'To Do';
    public const STATUS_PROGRESS = 'In Progress';
    public const STATUS_REVIEW   = 'Review';
    public const STATUS_DONE     = 'Done';

    public const STATUSES = [
        self::STATUS_TODO,
        self::STATUS_PROGRESS,
        self::STATUS_REVIEW,
        self::STATUS_DONE,
    ];

    public const PRIORITIES = ['low', 'medium', 'high'];

    // ✅ helper chuẩn hóa time về HH:MM (chống 09:15 vs 09:15:00)
    public static function normalizeTime(string $time): string
    {
        return substr(trim($time), 0, 5);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applyStatusTransition(string $newStatus): void
    {
        $now = now();

        if ($this->status === $newStatus) return;

        if ($this->status === self::STATUS_TODO && $newStatus === self::STATUS_PROGRESS) {
            $this->started_at ??= $now;
        }
        if ($this->status === self::STATUS_PROGRESS && $newStatus === self::STATUS_REVIEW) {
            $this->reviewed_at ??= $now;
        }
        if ($this->status === self::STATUS_REVIEW && $newStatus === self::STATUS_DONE) {
            $this->completed_at ??= $now;
        }

        // tiện ích cho “nhảy nhanh”
        if ($newStatus === self::STATUS_PROGRESS) $this->started_at ??= $now;
        if ($newStatus === self::STATUS_REVIEW)   $this->reviewed_at ??= $now;
        if ($newStatus === self::STATUS_DONE)     $this->completed_at ??= $now;

        $this->status = $newStatus;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_TODO     => 'badge-todo',
            self::STATUS_PROGRESS => 'badge-progress',
            self::STATUS_REVIEW   => 'badge-review',
            self::STATUS_DONE     => 'badge-done',
            default               => 'badge-secondary',
        };
    }
}
