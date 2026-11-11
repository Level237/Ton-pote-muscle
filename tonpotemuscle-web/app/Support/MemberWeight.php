<?php

namespace App\Support;

use App\Models\User;
use App\Models\WeightLog;
use Illuminate\Support\Carbon;

final class MemberWeight
{
    public static function weekStart(?Carbon $date = null): Carbon
    {
        return ($date ?? now())->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
    }

    public static function record(User $user, float|int|string $kg, ?Carbon $weighedOn = null): WeightLog
    {
        $weighedOn = ($weighedOn ?? now())->copy()->startOfDay();
        $weight = round((float) $kg, 1);

        $log = WeightLog::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'week_start' => self::weekStart($weighedOn)->toDateString(),
            ],
            [
                'weighed_on' => $weighedOn->toDateString(),
                'weight_kg' => $weight,
            ],
        );

        $user->forceFill(['weight_kg' => $weight])->save();

        return $log;
    }

    public static function format(float|int|string|null $kg): string
    {
        if ($kg === null || $kg === '') {
            return '';
        }

        $formatted = number_format((float) $kg, 1, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',').' kg';
    }
}
