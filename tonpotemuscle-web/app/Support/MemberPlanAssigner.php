<?php

namespace App\Support;

use App\Enums\EnrollmentStatus;
use App\Models\MemberAssignment;
use App\Models\MemberPlanItem;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class MemberPlanAssigner
{
    /**
     * @param  list<Carbon|string>  $dates
     * @param  array<string, float|int|string>  $loads
     */
    public function assignWorkout(User $member, User $admin, Workout $workout, array $dates, array $loads = []): MemberAssignment
    {
        $days = collect($dates)
            ->map(fn ($date) => Carbon::parse($date)->startOfDay())
            ->unique(fn (Carbon $date) => $date->toDateString())
            ->sort()
            ->values();

        if ($days->isEmpty()) {
            throw ValidationException::withMessages([
                'planned_on' => 'Choisis au moins un jour.',
            ]);
        }

        return DB::transaction(function () use ($member, $admin, $workout, $days, $loads) {
            $assignment = MemberAssignment::query()->create([
                'user_id' => $member->id,
                'assigned_by' => $admin->id,
                'type' => 'workout',
                'workout_id' => $workout->id,
                'starts_on' => $days->first()->toDateString(),
                'ends_on' => $days->last()->toDateString(),
            ]);

            foreach ($days as $on) {
                $this->replaceItem($member, $assignment, $on, 'workout', $workout->id, $loads);
            }

            return $assignment;
        });
    }

    /**
     * @param  array<string, float|int|string>  $exerciseLoads
     */
    public function assignProgramWeek(
        User $member,
        User $admin,
        Program $program,
        int $week,
        Carbon $monday,
        Carbon $from,
        array $exerciseLoads = [],
    ): MemberAssignment {
        return DB::transaction(function () use ($member, $admin, $program, $week, $monday, $from, $exerciseLoads) {
            $enrollment = ProgramEnrollment::query()->firstOrCreate(
                [
                    'user_id' => $member->id,
                    'program_id' => $program->id,
                ],
                [
                    'status' => EnrollmentStatus::Active,
                    'enrolled_at' => now(),
                ],
            );

            if ($enrollment->status !== EnrollmentStatus::Active) {
                $enrollment->update([
                    'status' => EnrollmentStatus::Active,
                    'abandoned_at' => null,
                ]);
            }

            $days = ProgramWeeks::days($program, $week);
            $dates = [];

            foreach ($days as $day) {
                $date = $monday->copy()->addDays($day['offset']);
                if ($date->lt($from->copy()->startOfDay())) {
                    continue;
                }
                $dates[] = $date;
            }

            if ($dates === []) {
                throw ValidationException::withMessages([
                    'target_week' => 'Aucun jour restant sur cette période.',
                ]);
            }

            $assignment = MemberAssignment::query()->create([
                'user_id' => $member->id,
                'assigned_by' => $admin->id,
                'type' => 'program_week',
                'program_id' => $program->id,
                'program_week' => $week,
                'starts_on' => ($dates[0] ?? $monday)->toDateString(),
                'ends_on' => (end($dates) ?: $monday)->toDateString(),
            ]);

            $workoutIds = $days->pluck('workout_id')->filter()->unique()->values();
            $workouts = $workoutIds->isEmpty()
                ? collect()
                : Workout::query()
                    ->with('items.exercise')
                    ->whereIn('id', $workoutIds->all())
                    ->get()
                    ->keyBy('id');

            foreach ($days as $day) {
                $date = $monday->copy()->addDays($day['offset']);
                if ($date->lt($from->copy()->startOfDay())) {
                    continue;
                }

                $workout = $day['workout_id'] ? $workouts->get($day['workout_id']) : null;
                $loads = $workout
                    ? MemberEquipment::loadsForWorkout($workout, $exerciseLoads)
                    : [];

                $this->replaceItem(
                    $member,
                    $assignment,
                    $date,
                    $day['is_rest'] ? 'rest' : 'workout',
                    $day['is_rest'] ? null : $day['workout_id'],
                    $loads,
                );
            }

            return $assignment;
        });
    }

    /**
     * @param  array<string, float|int|string>  $loads
     */
    private function replaceItem(
        User $member,
        MemberAssignment $assignment,
        Carbon $on,
        string $kind,
        ?string $workoutId,
        array $loads = [],
    ): void {
        MemberPlanItem::query()
            ->where('user_id', $member->id)
            ->whereDate('planned_on', $on->toDateString())
            ->delete();

        MemberPlanItem::query()->create([
            'user_id' => $member->id,
            'member_assignment_id' => $assignment->id,
            'planned_on' => $on->toDateString(),
            'kind' => $kind,
            'workout_id' => $workoutId,
            'loads' => $kind === 'workout' && $loads !== [] ? $loads : null,
        ]);
    }
}
