<?php

namespace App\Support;

use App\Models\RankingSnapshotEntry;
use App\Models\WorkoutLog;

final class ShareCopy
{
    public static function workoutTitle(WorkoutLog $log): string
    {
        $name = self::displayName($log->member?->name, $log->member?->username);
        $workout = $log->workout?->title ?? 'une séance';

        return "{$name} a effectué {$workout}";
    }

    public static function workoutText(WorkoutLog $log): string
    {
        $minutes = $log->durationMinutes();
        $mode = $log->mode === 'live' ? 'live' : 'déclarée';
        $points = (int) $log->points_awarded;

        return self::workoutTitle($log).". {$minutes} min, {$mode}, +{$points} pts.";
    }

    public static function rankTitle(RankingSnapshotEntry $entry): string
    {
        $name = self::displayName($entry->user?->name, $entry->username);
        $rank = self::ordinal((int) $entry->rank);
        $program = $entry->snapshot?->program?->title ?? 'un programme';

        return "{$name} termine {$rank} du programme {$program}";
    }

    public static function rankText(RankingSnapshotEntry $entry): string
    {
        return self::rankTitle($entry).". {$entry->points} pts.";
    }

    public static function ordinal(int $rank): string
    {
        return $rank === 1 ? '1er' : $rank.'e';
    }

    private static function displayName(?string $name, ?string $username): string
    {
        $name = trim((string) $name);

        if ($name !== '') {
            return $name;
        }

        $username = trim((string) $username);

        return $username !== '' ? $username : 'Un membre';
    }
}
