<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatus;
use App\Enums\ProgramStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiveMemberPlanRequest;
use App\Models\MemberPlanItem;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutCategory;
use App\Support\MemberPlanAssigner;
use App\Support\MemberTraining;
use App\Support\ProgramWeeks;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $deactivated = $request->boolean('deactivated');
        $search = $request->string('q')->trim()->toString();

        $members = User::query()
            ->members()
            ->when(
                $deactivated,
                fn ($query) => $query->deactivated(),
                fn ($query) => $query->active(),
            )
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'deactivated' => $deactivated,
            'search' => $search,
        ]);
    }

    public function show(Request $request, User $user): View
    {
        abort_unless($user->isMember(), 404);

        return view('admin.members.show', array_merge(
            MemberTraining::board($user, $request),
            ['member' => $user],
        ));
    }

    public function give(User $user): View
    {
        abort_unless($user->isMember() && ! $user->isDeactivated(), 404);

        $programs = Program::query()
            ->where('status', '!=', ProgramStatus::Draft)
            ->with('schedule.workout')
            ->latest()
            ->get()
            ->map(function (Program $program) {
                $weekCount = ProgramWeeks::count($program);
                $weeks = [];

                for ($week = 1; $week <= $weekCount; $week++) {
                    $weeks[$week] = ProgramWeeks::days($program, $week);
                }

                return [
                    'program' => $program,
                    'week_count' => $weekCount,
                    'weeks' => $weeks,
                ];
            });

        $currentEnrollment = ProgramEnrollment::query()
            ->where('user_id', $user->id)
            ->where('status', EnrollmentStatus::Active)
            ->with('program.schedule.workout')
            ->latest('enrolled_at')
            ->first();

        $currentWeek = null;
        if ($currentEnrollment?->program) {
            $currentWeek = ProgramWeeks::currentWeek($currentEnrollment->program);
        }

        $history = MemberPlanItem::query()
            ->where('user_id', $user->id)
            ->with('workout')
            ->orderByDesc('planned_on')
            ->limit(30)
            ->get();

        return view('admin.members.give', [
            'member' => $user,
            'categories' => WorkoutCategory::query()->orderBy('title')->get(['id', 'title']),
            'workouts' => Workout::query()->with('category')->orderBy('title')->get(['id', 'title', 'category_id']),
            'programs' => $programs,
            'history' => $history,
            'currentEnrollment' => $currentEnrollment,
            'currentWeek' => $currentWeek,
            'thisMonday' => ProgramWeeks::monday(),
            'nextMonday' => ProgramWeeks::monday(next: true),
        ]);
    }

    public function storeGive(GiveMemberPlanRequest $request, User $user, MemberPlanAssigner $assigner): RedirectResponse
    {
        abort_unless($user->isMember() && ! $user->isDeactivated(), 404);

        if ($request->input('mode') === 'workout') {
            $workout = Workout::query()->findOrFail($request->input('workout_id'));
            $dates = $request->input('planned_on', []);
            $assigner->assignWorkout($user, $request->user(), $workout, is_array($dates) ? $dates : [$dates]);

            return redirect()
                ->route('admin.enrollments.give', $user)
                ->with('status', 'Séance donnée.');
        }

        [$program, $week] = $this->resolveProgramWeek($request, $user);
        $next = $request->input('target_week') === 'next';
        $monday = ProgramWeeks::monday(next: $next);
        $from = $next ? $monday : now()->startOfDay();

        $assigner->assignProgramWeek($user, $request->user(), $program, $week, $monday, $from);

        return redirect()
            ->route('admin.enrollments.show', $user)
            ->with('status', 'Semaine donnée.');
    }

    public function destroyPlanItem(User $user, MemberPlanItem $item): RedirectResponse
    {
        abort_unless($user->isMember(), 404);
        abort_unless($item->user_id === $user->id, 404);

        $item->delete();

        return redirect()
            ->route('admin.enrollments.give', $user)
            ->with('status', 'Séance retirée.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_unless($user->isMember() && ! $user->isDeactivated(), 404);

        $user->update(['deactivated_at' => now()]);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('status', 'Compte désactivé.');
    }

    public function activate(User $user): RedirectResponse
    {
        abort_unless($user->isMember() && $user->isDeactivated(), 404);

        $user->update(['deactivated_at' => null]);

        return redirect()
            ->route('admin.enrollments.index', ['deactivated' => 1])
            ->with('status', 'Compte réactivé.');
    }

    /**
     * @return array{0: Program, 1: int}
     */
    private function resolveProgramWeek(GiveMemberPlanRequest $request, User $user): array
    {
        $programId = $request->string('program_id')->toString();

        if ($programId === 'current') {
            $enrollment = ProgramEnrollment::query()
                ->where('user_id', $user->id)
                ->where('status', EnrollmentStatus::Active)
                ->with('program.schedule')
                ->latest('enrolled_at')
                ->first();

            if ($enrollment?->program === null) {
                throw ValidationException::withMessages([
                    'program_id' => 'Cet inscrit n’a pas encore de programme en cours.',
                ]);
            }

            return [$enrollment->program, ProgramWeeks::currentWeek($enrollment->program)];
        }

        $program = Program::query()
            ->where('status', '!=', ProgramStatus::Draft)
            ->with('schedule.workout')
            ->findOrFail($programId);

        $week = (int) ($request->input('program_week') ?: 1);
        $week = min(max(1, $week), ProgramWeeks::count($program));

        return [$program, $week];
    }
}
