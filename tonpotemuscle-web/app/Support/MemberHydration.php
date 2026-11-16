<?php

namespace App\Support;

use App\Models\HydrationLog;
use App\Models\ProgramWorkout;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class MemberHydration
{
    public const GLASS_ML = 250;

    public const DEFAULT_GOAL = 8;

    public const MIN_GOAL = 4;

    public const MAX_GOAL = 16;

    public const MAX_GLASSES = 24;

    public static function goal(User $user): int
    {
        $goal = (int) ($user->hydration_goal_glasses ?: self::DEFAULT_GOAL);

        return max(self::MIN_GOAL, min(self::MAX_GOAL, $goal));
    }

    public static function setGoal(User $user, int $glasses): User
    {
        $goal = max(self::MIN_GOAL, min(self::MAX_GOAL, $glasses));
        $user->forceFill(['hydration_goal_glasses' => $goal])->save();

        return $user;
    }

    public static function sip(User $user, int $delta, ?Carbon $day = null): HydrationLog
    {
        $day = ($day ?? now())->copy()->startOfDay();

        $log = HydrationLog::query()->firstOrNew([
            'user_id' => $user->id,
            'day' => $day->toDateString(),
        ]);

        $log->glasses = max(0, min(self::MAX_GLASSES, ((int) $log->glasses) + $delta));
        $log->save();

        return $log;
    }

    public static function formatLiters(int $glasses): string
    {
        $liters = ($glasses * self::GLASS_ML) / 1000;
        $formatted = number_format($liters, 2, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',').' L';
    }

    /**
     * @return Collection<string, HydrationLog>
     */
    public static function weekLogs(User $user, Carbon $monday): Collection
    {
        $from = $monday->copy()->startOfDay();
        $to = $from->copy()->addDays(6);

        return HydrationLog::query()
            ->where('user_id', $user->id)
            ->whereDate('day', '>=', $from->toDateString())
            ->whereDate('day', '<=', $to->toDateString())
            ->get()
            ->keyBy(fn (HydrationLog $log) => $log->day->toDateString());
    }

    /**
     * @return list<array{date: Carbon, key: string, letter: string, glasses: int, goal: int, is_today: bool}>
     */
    public static function week(User $user, Carbon $monday): array
    {
        $logs = self::weekLogs($user, $monday);
        $goal = self::goal($user);
        $days = [];

        for ($offset = 0; $offset < 7; $offset++) {
            $date = $monday->copy()->addDays($offset)->startOfDay();
            $key = $date->toDateString();
            $log = $logs->get($key);

            $days[] = [
                'date' => $date,
                'key' => $key,
                'letter' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 1)),
                'glasses' => (int) ($log?->glasses ?? 0),
                'goal' => $goal,
                'is_today' => $date->isToday(),
            ];
        }

        return $days;
    }
}
