<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CompleteLiveWorkoutRequest;
use App\Models\MemberPlanItem;
use App\Models\ProgramEnrollment;
use App\Models\WorkoutLog;
use App\Support\WorkoutPoints;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LiveWorkoutController extends Controller
{
    public function show(Request $request, MemberPlanItem $item): View
    {
        $item = $this->ownedWorkout($request, $item);
        $item->load(['workout.items.exercise', 'workout.category']);

        $log = $this->logFor($request, $item);

        return view('member.workouts.show', [
            'item' => $item,
            'workout' => $item->workout,
            'log' => $log,
            'done' => $log?->isCompleted() ?? false,
            'inProgress' => $log !== null && ! $log->isCompleted() && ! $log->abandoned,
        ]);
    }

    public function start(Request $request, MemberPlanItem $item): RedirectResponse
    {
        $item = $this->ownedWorkout($request, $item);
        $log = $this->logFor($request, $item);

        if ($log?->isCompleted()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Cette séance est déjà faite.');
        }

        $enrollmentId = null;
        if ($item->assignment?->program_id) {
            $enrollmentId = ProgramEnrollment::query()
                ->where('user_id', $request->user()->id)
                ->where('program_id', $item->assignment->program_id)
                ->value('id');
        }

        $attributes = [
            'mode' => 'live',
            'started_at' => $log?->started_at ?? now(),
            'completed_at' => null,
            'abandoned' => false,
            'program_id' => $item->assignment?->program_id,
            'program_enrollment_id' => $enrollmentId,
            'points_awarded' => 0,
        ];

        if ($log) {
            $log->update($attributes);
        } else {
            WorkoutLog::query()->create([
                'user_id' => $request->user()->id,
                'workout_id' => $item->workout_id,
                'performed_on' => $item->planned_on->toDateString(),
                ...$attributes,
            ]);
        }

        return redirect()->route('member.workouts.live', $item);
    }

    public function live(Request $request, MemberPlanItem $item): View|RedirectResponse
    {
        $item = $this->ownedWorkout($request, $item);
        $item->load(['workout.items.exercise']);
        $log = $this->logFor($request, $item);

        if ($log?->isCompleted()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Cette séance est déjà faite.');
        }

        if ($log === null || $log->abandoned) {
            return redirect()->route('member.workouts.show', $item);
        }

        return view('member.workouts.live', [
            'item' => $item,
            'workout' => $item->workout,
            'log' => $log,
        ]);
    }

    public function complete(CompleteLiveWorkoutRequest $request, MemberPlanItem $item): JsonResponse
    {
        $item = $this->ownedWorkout($request, $item);
        $log = $this->logFor($request, $item);

        if ($log === null || $log->isCompleted()) {
            return response()->json(['redirect' => route('dashboard')]);
        }

        $payload = $request->validated();
        $abandoned = (bool) $payload['abandoned'];

        if ($abandoned) {
            $log->delete();

            return response()->json([
                'redirect' => route('dashboard'),
            ]);
        }

        $skipped = collect($payload['exercises'] ?? [])->contains(fn (array $row) => $row['skipped']);

        DB::transaction(function () use ($log, $item, $payload, $skipped): void {
            $log->exercises()->delete();

            foreach ($payload['exercises'] ?? [] as $row) {
                $log->exercises()->create([
                    'workout_exercise_id' => $row['workout_exercise_id'] ?? null,
                    'exercise_id' => $row['exercise_id'],
                    'position' => $row['position'],
                    'started_at' => $row['started_at'] ?? null,
                    'ended_at' => $row['ended_at'] ?? null,
                    'skipped' => $row['skipped'],
                ]);
            }

            $log->update([
                'completed_at' => now(),
                'abandoned' => false,
                'actual_duration_seconds' => $payload['actual_duration_seconds'] ?? null,
                'points_awarded' => WorkoutPoints::live($item->planned_on, $skipped),
            ]);
        });

        return response()->json([
            'redirect' => route('dashboard'),
        ]);
    }

    private function ownedWorkout(Request $request, MemberPlanItem $item): MemberPlanItem
    {
        abort_unless($item->user_id === $request->user()->id, 404);
        abort_if($item->isRest() || $item->workout_id === null, 404);

        $item->loadMissing('assignment');

        return $item;
    }

    private function logFor(Request $request, MemberPlanItem $item): ?WorkoutLog
    {
        return WorkoutLog::query()
            ->where('user_id', $request->user()->id)
            ->where('workout_id', $item->workout_id)
            ->whereDate('performed_on', $item->planned_on->toDateString())
            ->first();
    }
}
