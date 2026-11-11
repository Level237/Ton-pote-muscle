<?php

namespace App\Support;

final class WorkoutFeedback
{
    public const Easy = 1;

    public const Medium = 2;

    public const Hard = 3;

    /** @var array<int, string> */
    public const LABELS = [
        self::Easy => 'Très facile',
        self::Medium => 'Moyen',
        self::Hard => 'Difficile',
    ];

    /**
     * @return list<int>
     */
    public static function values(): array
    {
        return array_keys(self::LABELS);
    }
}
