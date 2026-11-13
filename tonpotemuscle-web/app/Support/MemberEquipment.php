<?php

namespace App\Support;

use App\Enums\BilanQuestionType;
use App\Models\Bilan;
use App\Models\BilanQuestion;
use App\Models\User;
use App\Models\Workout;

final class MemberEquipment
{
    /**
     * @param  array<int|string, mixed>  $answers
     */
    public static function syncFromBilan(User $user, Bilan $bilan, array $answers): void
    {
        $bilan->loadMissing('questions');

        foreach ($bilan->questions as $question) {
            $value = trim((string) ($answers[$question->id] ?? ''));

            if (self::isHasEquipmentQuestion($question)) {
                $parsed = self::parseYesNo($value);

                if ($parsed !== null) {
                    $user->trains_with_equipment = $parsed;
                } elseif ($value !== '') {
                    $user->trains_with_equipment = true;
                    $user->equipment_notes = $value;
                }
            }

            if (self::isDetailsQuestion($question)) {
                $user->equipment_notes = $value !== '' ? $value : null;
            }
        }

        $user->save();
    }

    public static function declined(User $member): bool
    {
        return in_array($member->trains_with_equipment, [false, 0, '0'], true);
    }

    public static function statusLabel(User $member): string
    {
        if (self::declined($member)) {
            return 'Non';
        }

        if (in_array($member->trains_with_equipment, [true, 1, '1'], true)) {
            return 'Oui';
        }

        return 'Non renseigné';
    }

    public static function formatLoad(float|int|string|null $kg): ?string
    {
        if ($kg === null || $kg === '') {
            return null;
        }

        $formatted = number_format((float) $kg, 1, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',').' kg';
    }

    public static function parseYesNo(string $value): ?bool
    {
        $normalized = mb_strtolower(trim($value));

        return match ($normalized) {
            'oui', 'yes', '1' => true,
            'non', 'no', '0' => false,
            default => null,
        };
    }

    public static function isHasEquipmentQuestion(BilanQuestion $question): bool
    {
        $title = mb_strtolower($question->title);

        if (str_contains($title, 'précise')) {
            return false;
        }

        if (! str_contains($title, 'matériel')) {
            return false;
        }

        if (str_contains($title, 'travailles') || str_contains($title, 'disponible')) {
            return true;
        }

        $options = $question->options ?? [];

        return $question->type === BilanQuestionType::Choice
            && in_array('Oui', $options, true);
    }

    public static function isDetailsQuestion(BilanQuestion $question): bool
    {
        $title = mb_strtolower($question->title);

        return str_contains($title, 'précise') && str_contains($title, 'matériel');
    }

    /**
     * @param  iterable<int, Workout|null>  $workouts
     * @return list<array{id: string, title: string}>
     */
    public static function uniqueLoadableExercises(iterable $workouts): array
    {
        $seen = [];
        $rows = [];

        foreach ($workouts as $workout) {
            if ($workout === null) {
                continue;
            }

            $workout->loadMissing('items.exercise');

            foreach ($workout->items as $item) {
                if (! $item->exercise?->usesLoad()) {
                    continue;
                }

                $id = (string) $item->exercise_id;

                if (isset($seen[$id])) {
                    continue;
                }

                $seen[$id] = true;
                $rows[] = [
                    'id' => $id,
                    'title' => $item->exercise->title,
                ];
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, float|int|string>  $exerciseLoads
     * @return array<string, float>
     */
    public static function loadsForWorkout(Workout $workout, array $exerciseLoads): array
    {
        $workout->loadMissing('items.exercise');
        $loads = [];

        foreach ($workout->items as $row) {
            if (! $row->exercise?->usesLoad()) {
                continue;
            }

            $kg = $exerciseLoads[(string) $row->exercise_id] ?? null;

            if ($kg === null || $kg === '') {
                continue;
            }

            $loads[(string) $row->id] = round((float) $kg, 1);
        }

        return $loads;
    }
}
