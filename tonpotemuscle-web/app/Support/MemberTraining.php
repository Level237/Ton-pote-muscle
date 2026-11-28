<?php

namespace App\Support;

use App\Models\MemberPlanItem;
use App\Models\ProgramEnrollment;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WeightLog;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class MemberTraining
{
    /**
     * @param  Collection<int, MemberPlanItem>  $items
     * @param  Collection<int, WorkoutLog>  $logs
     * @return Collection<int, array{
     *     date: Carbon,
     *     key: string,
     *     weekday: string,
     *     weekday_short: string,
     *     letter: string,
     *     item: MemberPlanItem|null,
     *     log: WorkoutLog|null,
     *     done: bool,
     *     status: string,
     *     is_today: bool
     * }>
     */
    public static function days(Carbon $monday, Collection $items, Collection $logs): Collection
    {
        $byDate = $items->keyBy(fn (MemberPlanItem $item) => $item->planned_on->toDateString());
        $logsByKey = $logs
            ->filter(fn (WorkoutLog $log) => $log->isCompleted())
            ->keyBy(fn (WorkoutLog $log) => $log->workout_id.'|'.$log->performed_on->toDateString());

        $days = collect();

        for ($offset = 0; $offset < 7; $offset++) {
            $date = $monday->copy()->addDays($offset)->startOfDay();
            $key = $date->toDateString();
            $item = $byDate->get($key);
            $log = $item?->workout_id
                ? $logsByKey->get($item->workout_id.'|'.$key)
                : null;

            $days->push([
                'date' => $date,
                'key' => $key,
                'weekday' => ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '',
                'weekday_short' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 3)),
                'letter' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 1)),
                'item' => $item,
                'log' => $log,
                'done' => $log !== null,
                'status' => self::status($item, $log !== null, $date),
                'is_today' => $date->isToday(),
            ]);
        }

        return $days;
    }

    public static function status(?MemberPlanItem $item, bool $done, Carbon $date): string
    {
        if ($item === null || $item->isRest()) {
            return 'rest';
        }

        if ($done) {
            return 'done';
        }

        if ($date->isToday()) {
            return 'today';
        }

        if ($date->lt(now()->startOfDay())) {
            return 'missed';
        }

        return 'todo';
    }

    /**
     * @param  Collection<int, WorkoutLog>  $logs
     * @return array{count: int, previous: int, bars: list<array{label: string, count: int, current: bool}>}
     */
    public static function progress(Collection $logs, string $range, Carbon $now): array
    {
        $completed = $logs->filter(fn (WorkoutLog $log) => $log->isCompleted());

        if ($range === 'week') {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);
            $previousStart = $start->copy()->subWeek();
            $bars = [];

            for ($offset = 0; $offset < 7; $offset++) {
                $day = $start->copy()->addDays($offset);
                $bars[] = [
                    'label' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$day->dayOfWeekIso] ?? '', 0, 1)),
                    'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->isSameDay($day))->count(),
                    'current' => $day->isToday(),
                ];
            }

            return [
                'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->betweenIncluded($start, $start->copy()->addDays(6)))->count(),
                'previous' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->betweenIncluded($previousStart, $previousStart->copy()->addDays(6)))->count(),
                'bars' => $bars,
            ];
        }

        if ($range === 'year') {
            $year = $now->year;
            $bars = [];

            for ($month = 1; $month <= 12; $month++) {
                $bars[] = [
                    'label' => ProgramWeeks::MONTHS_SHORT[$month],
                    'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->year === $year && $log->performed_on->month === $month)->count(),
                    'current' => $month === $now->month,
                ];
            }

            return [
                'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->year === $year)->count(),
                'previous' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->year === $year - 1)->count(),
                'bars' => $bars,
            ];
        }

        $bars = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->startOfMonth()->subMonths($i);
            $bars[] = [
                'label' => ProgramWeeks::MONTHS_SHORT[$month->month],
                'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->isSameMonth($month))->count(),
                'current' => $i === 0,
            ];
        }

        $currentMonth = $now->copy()->startOfMonth();
        $previousMonth = $currentMonth->copy()->subMonth();

        return [
            'count' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->isSameMonth($currentMonth))->count(),
            'previous' => $completed->filter(fn (WorkoutLog $log) => $log->performed_on->isSameMonth($previousMonth))->count(),
            'bars' => $bars,
        ];
    }

    /**
     * @return array{
     *     view: string,
     *     range: string,
     *     monday: Carbon,
     *     days: Collection<int, array<string, mixed>>,
     *     weekWorkouts: int,
     *     weekDone: int,
     *     catchUp: Collection<int, MemberPlanItem>,
     *     progress: array{count: int, previous: int, bars: list<array{label: string, count: int, current: bool}>},
     *     enrollments: Collection<int, ProgramEnrollment>,
     *     weightLogs: Collection<int, WeightLog>,
     *     weekWeight: WeightLog|null,
     *     hydrationGoal: int,
     *     todayHydration: int,
     *     weekHydration: list<array{date: Carbon, key: string, letter: string, glasses: int, goal: int, is_today: bool}>,
     *     weekMeals: list<array<string, mixed>>,
     *     todayMeals: array<string, mixed>|null,
     *     suivi: string
     * }
     */
    public static function board(User $member, Request $request): array
    {
        $view = in_array($request->query('vue'), ['accueil', 'semaine', 'progres'], true)
            ? $request->query('vue')
            : 'accueil';
        $range = in_array($request->query('range'), ['week', 'month', 'year'], true)
            ? $request->query('range')
            : 'month';
        $suivi = in_array($request->query('suivi'), ['pese', 'repas'], true)
            ? $request->query('suivi')
            : 'eau';

        $monday = ProgramWeeks::monday();
        if ($request->filled('week')) {
            $monday = $request->date('week')->startOfWeek(Carbon::MONDAY)->startOfDay();
        }

        $items = MemberPlanItem::query()
            ->where('user_id', $member->id)
            ->whereDate('planned_on', '>=', $monday->copy()->subWeeks(8)->toDateString())
            ->whereDate('planned_on', '<=', $monday->copy()->addWeeks(8)->toDateString())
            ->with(['workout.items.exercise', 'workout.category', 'assignment.program'])
            ->orderBy('planned_on')
            ->get();

        $logs = WorkoutLog::query()
            ->where('user_id', $member->id)
            ->where('abandoned', false)
            ->whereDate('performed_on', '>=', now()->subYear()->startOfYear()->toDateString())
            ->get();

        $days = self::days($monday, $items, $logs);
        $weekWorkouts = $days->filter(fn (array $day) => $day['status'] !== 'rest');

        $weightLogs = WeightLog::query()
            ->where('user_id', $member->id)
            ->orderByDesc('week_start')
            ->limit(8)
            ->get();
        $weekStart = MemberWeight::weekStart()->toDateString();
        $weekWeight = $weightLogs->first(
            fn (WeightLog $log) => $log->week_start->toDateString() === $weekStart,
        );

        $catchUp = $items
            ->filter(function (MemberPlanItem $item) use ($logs): bool {
                if (! $item->isCatchUp() || $item->isProgramClosed()) {
                    return false;
                }

                return ! $logs->contains(function (WorkoutLog $log) use ($item): bool {
                    return $log->isCompleted()
                        && $log->workout_id === $item->workout_id
                        && $log->performed_on->isSameDay($item->planned_on);
                });
            })
            ->values();

        $currentMonday = ProgramWeeks::monday();
        $weekHydration = MemberHydration::week($member, $currentMonday);
        $weekMeals = MemberMeals::week($member, $currentMonday);
        $member->loadMissing('mealPlan');

        return [
            'view' => $view,
            'range' => $range,
            'monday' => $monday,
            'days' => $days,
            'weekWorkouts' => $weekWorkouts->count(),
            'weekDone' => $weekWorkouts->filter(fn (array $day) => $day['done'])->count(),
            'catchUp' => $catchUp,
            'caughtUpToday' => self::hasCaughtUpToday($member),
            'progress' => self::progress($logs, $range, now()),
            'enrollments' => ProgramEnrollment::query()
                ->where('user_id', $member->id)
                ->with('program')
                ->latest('enrolled_at')
                ->get(),
            'weightLogs' => $weightLogs,
            'weekWeight' => $weekWeight,
            'hydrationGoal' => MemberHydration::goal($member),
            'weekHydration' => $weekHydration,
            'todayHydration' => (int) (collect($weekHydration)->firstWhere('is_today')['glasses'] ?? 0),
            'weekMeals' => $weekMeals,
            'todayMeals' => collect($weekMeals)->firstWhere('is_today'),
            'mealPlan' => $member->mealPlan,
            'suivi' => $suivi,
        ];
    }

    public static function hasCaughtUpToday(User $member, ?MemberPlanItem $excluding = null): bool
    {
        $today = now()->toDateString();

        return WorkoutLog::query()
            ->where('user_id', $member->id)
            ->where('abandoned', false)
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $today)
            ->whereDate('performed_on', '<', $today)
            ->when($excluding !== null, function ($query) use ($excluding): void {
                $query->where(function ($sub) use ($excluding): void {
                    $sub->where('workout_id', '!=', $excluding->workout_id)
                        ->orWhereDate('performed_on', '!=', $excluding->planned_on->toDateString());
                });
            })
            ->exists();
    }
}
