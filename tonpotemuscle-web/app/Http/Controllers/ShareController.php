<?php

namespace App\Http\Controllers;

use App\Models\RankingSnapshotEntry;
use App\Models\WorkoutLog;
use App\Support\ShareCopy;
use Illuminate\View\View;

class ShareController extends Controller
{
    public function __invoke(string $token): View
    {
        $log = WorkoutLog::query()
            ->with(['member', 'workout'])
            ->where('share_token', $token)
            ->whereNotNull('completed_at')
            ->where('abandoned', false)
            ->first();

        if ($log) {
            return view('share.workout', [
                'log' => $log,
                'shareUrl' => route('share.show', $token),
                'shareText' => ShareCopy::workoutText($log),
                'shareTitle' => ShareCopy::workoutTitle($log),
            ]);
        }

        $entry = RankingSnapshotEntry::query()
            ->with(['user', 'snapshot.program'])
            ->where('share_token', $token)
            ->first();

        abort_unless($entry?->snapshot?->program, 404);

        return view('share.rank', [
            'entry' => $entry,
            'program' => $entry->snapshot->program,
            'shareUrl' => route('share.show', $token),
            'shareText' => ShareCopy::rankText($entry),
            'shareTitle' => ShareCopy::rankTitle($entry),
        ]);
    }
}
