<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    protected static function booted()
{
    static::saving(function ($task) {
        if ($task->repeat_weekly && empty($task->series_id)) {
            $task->series_id = (string) Str::uuid();
        }
    });
}
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
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at'     => 'datetime',
        'reviewed_at'    => 'datetime',
        'completed_at'   => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Business rule: set timestamps when moving forward in the workflow.
     * NOTE: Do not delete old timestamps when moving backward.
     */
    public function applyStatusTransition(string $newStatus): void
    {
        $now = now(); // respects app timezone

        if ($this->status === $newStatus) {
            return;
        }

        // Forward transitions timestamps
        if ($this->status === self::STATUS_TODO && $newStatus === self::STATUS_PROGRESS) {
            $this->started_at ??= $now;
        }

        if ($this->status === self::STATUS_PROGRESS && $newStatus === self::STATUS_REVIEW) {
            $this->reviewed_at ??= $now;
        }

        if ($this->status === self::STATUS_REVIEW && $newStatus === self::STATUS_DONE) {
            $this->completed_at ??= $now;
        }

        // Also allow quick jumps forward (optional convenience)
        if ($newStatus === self::STATUS_PROGRESS) {
            $this->started_at ??= $now;
        }
        if ($newStatus === self::STATUS_REVIEW) {
            $this->reviewed_at ??= $now;
        }
        if ($newStatus === self::STATUS_DONE) {
            $this->completed_at ??= $now;
        }

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
