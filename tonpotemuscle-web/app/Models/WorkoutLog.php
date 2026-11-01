<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'workout_id',
    'program_enrollment_id',
    'program_id',
    'program_workout_id',
    'mode',
    'performed_on',
    'started_at',
    'completed_at',
    'actual_duration_seconds',
    'difficulty_feedback',
    'notes',
    'abandoned',
    'points_awarded',
])]
class WorkoutLog extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_on' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'abandoned' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Workout, $this>
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function isCompleted(): bool
    {
        return ! $this->abandoned;
    }
}
