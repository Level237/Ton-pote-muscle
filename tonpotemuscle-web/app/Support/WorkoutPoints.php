<?php

namespace App\Support;

use Illuminate\Support\Carbon;

final class WorkoutPoints
{
    public static function live(
        Carbon $plannedOn,
        bool $skipped,
        bool $abandoned = false,
        bool $programClosed = false,
    ): int {
        if ($abandoned || $programClosed) {
            return 0;
        }

        $today = now()->startOfDay();
        $planned = $plannedOn->copy()->startOfDay();
        $diff = (int) abs($planned->diffInDays($today));

        if ($diff === 0) {
            $base = 10;
        } elseif ($diff === 1) {
            $base = 7;
        } elseif ($planned->lt($today)) {
            $base = 4;
        } else {
            return 0;
        }

        return $skipped ? $base : $base + 2;
    }
}
