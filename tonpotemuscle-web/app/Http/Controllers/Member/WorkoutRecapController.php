<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreWorkoutFeedbackRequest;
use App\Models\MemberPlanItem;
use App\Models\WorkoutLog;
use App\Support\ShareCopy;
use App\Support\WorkoutFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkoutRecapController extends Controller
{
    public function show(Request $request, MemberPlanItem $item): View
    {
        $log = $this->completedLog($request, $item);
        $log->ensureShareToken();

        return view('member.workouts.recap', [
            'item' => $item,
            'log' => $log,
            'feedbacks' => WorkoutFeedback::LABELS,
            'shareUrl' => $log->shareUrl(),
            'shareText' => ShareCopy::workoutText($log),
            'shareTitle' => ShareCopy::workoutTitle($log),
        ]);
    }

    public function store(StoreWorkoutFeedbackRequest $request, MemberPlanItem $item): RedirectResponse
    {
        $log = $this->completedLog($request, $item);

        $log->update([
            'difficulty_feedback' => (int) $request->validated('difficulty_feedback'),
        ]);

        return redirect()->route('member.workouts.recap', $item);
    }

    private function completedLog(Request $request, MemberPlanItem $item): WorkoutLog
    {
        abort_unless($item->user_id === $request->user()->id, 404);
        abort_if($item->isRest() || $item->workout_id === null, 404);

        $log = WorkoutLog::query()
            ->with(['member', 'workout'])
            ->where('user_id', $request->user()->id)
            ->where('workout_id', $item->workout_id)
            ->whereDate('performed_on', $item->planned_on->toDateString())
            ->first();

        abort_unless($log?->isCompleted(), 404);

        return $log;
    }
}
