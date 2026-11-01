<?php

namespace App\Support;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\RankingSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class ProgramRanking
{
    /**
     * @return Collection<int, array{rank: int, user_id: int, username: string, points: int, live_count: int, enrolled_at: Carbon|null}>
     */
    public static function for(Program $program): Collection
    {
        $program->loadMissing('rankingSnapshot.entries');

        if ($program->rankingSnapshot !== null) {
            return self::fromSnapshot($program->rankingSnapshot);
        }

        return self::live($program);
    }

    /**
     * @return Collection<int, array{rank: int, user_id: int, username: string, points: int, live_count: int, enrolled_at: Carbon|null}>
     */
    public static function live(Program $program): Collection
    {
        $enrollments = ProgramEnrollment::query()
            ->active()
            ->where('program_id', $program->id)
            ->with('user')
            ->orderByDesc('points')
            ->orderByDesc('live_count')
            ->orderBy('enrolled_at')
            ->orderBy('id')
            ->get();

        return $enrollments->values()->map(function (ProgramEnrollment $enrollment, int $index): array {
            return [
                'rank' => $index + 1,
                'user_id' => $enrollment->user_id,
                'username' => $enrollment->user->username ?: $enrollment->user->name,
                'points' => $enrollment->points,
                'live_count' => $enrollment->live_count,
                'enrolled_at' => $enrollment->enrolled_at,
            ];
        });
    }

    /**
     * @return Collection<int, array{rank: int, user_id: int, username: string, points: int, live_count: int, enrolled_at: Carbon|null}>
     */
    public static function fromSnapshot(RankingSnapshot $snapshot): Collection
    {
        return $snapshot->entries
            ->sortBy('rank')
            ->values()
            ->map(fn ($entry) => [
                'rank' => $entry->rank,
                'user_id' => $entry->user_id,
                'username' => $entry->username,
                'points' => $entry->points,
                'live_count' => $entry->live_count,
                'enrolled_at' => null,
            ]);
    }
}
