<?php

namespace App\Support;

use App\Models\MealLog;
use App\Models\MemberMealPlan;
use App\Models\ProgramWorkout;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class MemberMeals
{
    public const SLOTS = [
        'breakfast' => 'Petit-déj',
        'lunch' => 'Midi',
        'dinner' => 'Soir',
    ];

    public const STATUSES = [
        'tenu' => 'Tenu',
        'approx' => 'À peu près',
        'ecart' => 'Écart',
    ];

    public static function plan(User $user): ?MemberMealPlan
    {
        return $user->mealPlan;
    }

    /**
     * @param  array{breakfast?: ?string, lunch?: ?string, dinner?: ?string}  $slots
     */
    public static function give(User $member, User $coach, array $slots): MemberMealPlan
    {
        $clean = [];

        foreach (array_keys(self::SLOTS) as $slot) {
            $value = trim((string) ($slots[$slot] ?? ''));
            $clean[$slot] = $value === '' ? null : $value;
        }

        return MemberMealPlan::query()->updateOrCreate(
            ['user_id' => $member->id],
            [...$clean, 'assigned_by' => $coach->id],
        );
    }

    public static function isPrescribed(User $user, string $slot): bool
    {
        return filled(self::plan($user)?->titleFor($slot));
    }

    public static function today(User $user, ?Carbon $day = null): MealLog
    {
        $day = ($day ?? now())->copy()->startOfDay();

        return MealLog::query()->firstOrNew([
            'user_id' => $user->id,
            'day' => $day->toDateString(),
        ]);
    }

    public static function checkIn(User $user, string $slot, string $status, ?Carbon $day = null): MealLog
    {
        abort_unless(isset(self::SLOTS[$slot], self::STATUSES[$status]), 422);
        abort_unless(self::isPrescribed($user, $slot), 403);

        $log = self::today($user, $day);
        $log->{$slot} = $log->{$slot} === $status ? null : $status;
        $log->save();

        return $log;
    }

    public static function note(User $user, ?string $note, ?Carbon $day = null): MealLog
    {
        $log = self::today($user, $day);
        $trimmed = trim((string) $note);
        $log->note = $trimmed === '' ? null : $trimmed;
        $log->save();

        return $log;
    }

    /**
     * @return Collection<string, MealLog>
     */
    public static function weekLogs(User $user, Carbon $monday): Collection
    {
        $from = $monday->copy()->startOfDay();
        $to = $from->copy()->addDays(6);

        return MealLog::query()
            ->where('user_id', $user->id)
            ->whereDate('day', '>=', $from->toDateString())
            ->whereDate('day', '<=', $to->toDateString())
            ->get()
            ->keyBy(fn (MealLog $log) => $log->day->toDateString());
    }

    /**
     * @return list<array{date: Carbon, key: string, letter: string, breakfast: ?string, lunch: ?string, dinner: ?string, note: ?string, filled: int, ecarts: int, is_today: bool}>
     */
    public static function week(User $user, Carbon $monday): array
    {
        $logs = self::weekLogs($user, $monday);
        $days = [];

        for ($offset = 0; $offset < 7; $offset++) {
            $date = $monday->copy()->addDays($offset)->startOfDay();
            $key = $date->toDateString();
            $log = $logs->get($key);
            $slots = [
                'breakfast' => $log?->breakfast,
                'lunch' => $log?->lunch,
                'dinner' => $log?->dinner,
            ];

            $days[] = [
                'date' => $date,
                'key' => $key,
                'letter' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 1)),
                ...$slots,
                'note' => $log?->note,
                'filled' => collect($slots)->filter()->count(),
                'ecarts' => collect($slots)->filter(fn ($status) => $status === 'ecart')->count(),
                'is_today' => $date->isToday(),
            ];
        }

        return $days;
    }
}
