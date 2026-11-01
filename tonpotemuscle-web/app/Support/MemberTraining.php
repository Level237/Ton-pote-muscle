<?php

namespace App\Support;

use App\Models\MemberPlanItem;
use App\Models\ProgramEnrollment;
use App\Models\ProgramWorkout;
use App\Models\User;
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
     *     done: bool,
     *     status: string,
     *     is_today: bool
     * }>
     */
    public static function days(Carbon $monday, Collection $items, Collection $logs): Collection
    {
        $byDate = $items->keyBy(fn (MemberPlanItem $item) => $item->planned_on->toDateString());
        $doneKeys = $logs
            ->filter(fn (WorkoutLog $log) => $log->isCompleted())
            ->mapWithKeys(fn (WorkoutLog $log) => [
                $log->workout_id.'|'.$log->performed_on->toDateString() => true,
            ]);

        $days = collect();

        for ($offset = 0; $offset < 7; $offset++) {
            $date = $monday->copy()->addDays($offset)->startOfDay();
            $key = $date->toDateString();
            $item = $byDate->get($key);
            $done = $item?->workout_id
                ? $doneKeys->has($item->workout_id.'|'.$key)
                : false;

            $days->push([
                'date' => $date,
                'key' => $key,
                'weekday' => ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '',
                'weekday_short' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 3)),
                'letter' => mb_strtoupper(mb_substr(ProgramWorkout::WEEKDAYS[$date->dayOfWeekIso] ?? '', 0, 1)),
                'item' => $item,
                'done' => $done,
                'status' => self::status($item, $done, $date),
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
     *     enrollments: Collection<int, ProgramEnrollment>
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

        $monday = ProgramWeeks::monday();
        if ($request->filled('week')) {
            $monday = $request->date('week')->startOfWeek(Carbon::MONDAY)->startOfDay();
        }

        $items = MemberPlanItem::query()
            ->where('user_id', $member->id)
            ->whereDate('planned_on', '>=', $monday->copy()->subWeeks(8)->toDateString())
            ->whereDate('planned_on', '<=', $monday->copy()->addWeeks(8)->toDateString())
            ->with(['workout.items.exercise', 'workout.category'])
            ->orderBy('planned_on')
            ->get();

        $logs = WorkoutLog::query()
            ->where('user_id', $member->id)
            ->where('abandoned', false)
            ->whereDate('performed_on', '>=', now()->subYear()->startOfYear()->toDateString())
            ->get();

        $days = self::days($monday, $items, $logs);
        $weekWorkouts = $days->filter(fn (array $day) => $day['status'] !== 'rest');

        $catchUp = $items
            ->filter(function (MemberPlanItem $item) use ($logs): bool {
                if ($item->isRest() || $item->planned_on->gte(now()->startOfDay())) {
                    return false;
                }

                return ! $logs->contains(function (WorkoutLog $log) use ($item): bool {
                    return $log->isCompleted()
                        && $log->workout_id === $item->workout_id
                        && $log->performed_on->isSameDay($item->planned_on);
                });
            })
            ->values();

        return [
            'view' => $view,
            'range' => $range,
            'monday' => $monday,
            'days' => $days,
            'weekWorkouts' => $weekWorkouts->count(),
            'weekDone' => $weekWorkouts->filter(fn (array $day) => $day['done'])->count(),
            'catchUp' => $catchUp,
            'progress' => self::progress($logs, $range, now()),
            'enrollments' => ProgramEnrollment::query()
                ->where('user_id', $member->id)
                ->with('program')
                ->latest('enrolled_at')
                ->get(),
        ];
    }
}
