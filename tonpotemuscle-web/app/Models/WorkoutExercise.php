<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'workout_id',
    'exercise_id',
    'position',
    'sets',
    'reps',
    'duration_seconds',
    'rest_seconds',
])]
class WorkoutExercise extends Model
{
    /**
     * @return BelongsTo<Workout, $this>
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * @return BelongsTo<Exercise, $this>
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function workLabel(): string
    {
        $sets = max(1, (int) $this->sets);

        if (filled($this->reps)) {
            return $sets.' × '.$this->reps;
        }

        if (filled($this->duration_seconds)) {
            return $sets.' × '.$this->duration_seconds.' s';
        }

        return $sets.' série'.($sets > 1 ? 's' : '');
    }

    public function restLabel(): string
    {
        $rest = max(0, (int) $this->rest_seconds);
        $minutes = intdiv($rest, 60);
        $seconds = $rest % 60;

        return $minutes.':'.str_pad((string) $seconds, 2, '0', STR_PAD_LEFT);
    }
}
