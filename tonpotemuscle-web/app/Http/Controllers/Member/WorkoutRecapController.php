<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberPlanItem;
use App\Models\WorkoutLog;
use App\Support\ShareCopy;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkoutRecapController extends Controller
{
    public function __invoke(Request $request, MemberPlanItem $item): View
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

        $log->ensureShareToken();

        return view('member.workouts.recap', [
            'item' => $item,
            'log' => $log,
            'shareUrl' => $log->shareUrl(),
            'shareText' => ShareCopy::workoutText($log),
            'shareTitle' => ShareCopy::workoutTitle($log),
        ]);
    }
}
