<?php

namespace App\Support;

use App\Models\RankingSnapshotEntry;
use App\Models\WorkoutLog;
use Illuminate\Support\Str;

final class ShareToken
{
    public static function unique(): string
    {
        do {
            $token = strtolower((string) Str::ulid());
        } while (
            WorkoutLog::query()->where('share_token', $token)->exists()
            || RankingSnapshotEntry::query()->where('share_token', $token)->exists()
        );

        return $token;
    }
}
