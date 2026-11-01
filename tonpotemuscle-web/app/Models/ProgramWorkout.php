<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'program_id',
    'workout_id',
    'day_number',
])]
class ProgramWorkout extends Model
{
    /**
     * @var array<int, string>
     */
    public const WEEKDAYS = [
        1 => 'lundi',
        2 => 'mardi',
        3 => 'mercredi',
        4 => 'jeudi',
        5 => 'vendredi',
        6 => 'samedi',
        7 => 'dimanche',
    ];

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo<Workout, $this>
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function isRest(): bool
    {
        return $this->workout_id === null;
    }

    public function label(): string
    {
        if ($this->isRest()) {
            return 'Repos';
        }

        return $this->workout?->title ?? 'Séance';
    }

    public function weekdayLabel(): ?string
    {
        $startsOn = $this->program?->starts_on;

        if ($startsOn === null || $this->day_number === null) {
            return null;
        }

        $date = $startsOn->copy()->addDays($this->day_number - 1);

        return self::WEEKDAYS[$date->dayOfWeekIso] ?? null;
    }
}
