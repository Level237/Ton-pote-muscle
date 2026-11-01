<?php

namespace App\Support;

use App\Models\Program;
use App\Models\ProgramWorkout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class ProgramWeeks
{
    /**
     * @var array<int, string>
     */
    public const MONTHS_SHORT = [
        1 => 'Janv.',
        2 => 'Févr.',
        3 => 'Mars',
        4 => 'Avr.',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juil.',
        8 => 'Août',
        9 => 'Sept.',
        10 => 'Oct.',
        11 => 'Nov.',
        12 => 'Déc.',
    ];

    public const MONTHS = [
        1 => 'janvier',
        2 => 'février',
        3 => 'mars',
        4 => 'avril',
        5 => 'mai',
        6 => 'juin',
        7 => 'juillet',
        8 => 'août',
        9 => 'septembre',
        10 => 'octobre',
        11 => 'novembre',
        12 => 'décembre',
    ];

    public static function count(Program $program): int
    {
        $max = (int) ($program->schedule->max('day_number') ?? 0);

        return max(1, (int) ceil($max / 7));
    }

    public static function currentWeek(Program $program, ?Carbon $from = null): int
    {
        $from ??= now();
        $origin = $program->starts_on?->copy()->startOfDay() ?? $from->copy()->startOfDay();
        $week = (int) floor($origin->diffInDays($from) / 7) + 1;

        return min(max(1, $week), self::count($program));
    }

    /**
     * @return Collection<int, array{offset: int, day_number: int, weekday: string, weekday_short: string, label: string, is_rest: bool, workout_id: string|null}>
     */
    public static function days(Program $program, int $week): Collection
    {
        $program->loadMissing('schedule.workout');

        $start = ($week - 1) * 7 + 1;
        $byDay = $program->schedule->keyBy('day_number');
        $days = collect();

        for ($offset = 0; $offset < 7; $offset++) {
            $dayNumber = $start + $offset;
            $slot = $byDay->get($dayNumber);
            $weekday = ProgramWorkout::WEEKDAYS[$offset + 1];

            $days->push([
                'offset' => $offset,
                'day_number' => $dayNumber,
                'weekday' => $weekday,
                'weekday_short' => mb_substr($weekday, 0, 3).'.',
                'label' => $slot?->label() ?? 'Repos',
                'is_rest' => $slot === null || $slot->isRest(),
                'workout_id' => $slot?->workout_id,
            ]);
        }

        return $days;
    }

    public static function monday(?Carbon $from = null, bool $next = false): Carbon
    {
        $monday = ($from ?? now())->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();

        return $next ? $monday->addWeek() : $monday;
    }

    public static function frenchDay(Carbon $date): string
    {
        $weekday = ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '';
        $short = mb_substr($weekday, 0, 3).'.';

        return $short.' '.$date->day.' '.(self::MONTHS[$date->month] ?? '');
    }

    public static function frenchLongDay(Carbon $date): string
    {
        if ($date->isToday()) {
            return 'aujourd’hui';
        }

        $weekday = ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '';

        return 'le '.$weekday.' '.$date->day.' '.(self::MONTHS[$date->month] ?? '');
    }
}
