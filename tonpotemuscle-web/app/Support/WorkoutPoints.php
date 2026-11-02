<?php

namespace App\Support;

use Illuminate\Support\Carbon;

final class WorkoutPoints
{
    public static function live(Carbon $plannedOn, bool $skipped, bool $abandoned = false): int
    {
        if ($abandoned) {
            return 0;
        }

        $diff = (int) abs($plannedOn->copy()->startOfDay()->diffInDays(now()->startOfDay()));

        $base = match ($diff) {
            0 => 10,
            1 => 7,
            default => 0,
        };

        if ($base === 0) {
            return 0;
        }

        return $skipped ? $base : $base + 2;
    }
}
