<?php

namespace App\Support;

final class ExerciseCatalog
{
    /** @var array<string, string> */
    public const BODY_PARTS = [
        'chest' => 'Poitrine',
        'back' => 'Dos',
        'shoulders' => 'Épaules',
        'arms' => 'Bras',
        'abs' => 'Abdos',
        'legs' => 'Jambes',
        'glutes' => 'Fessiers',
        'full_body' => 'Full body',
    ];

    /** @var array<string, string> */
    public const EQUIPMENTS = [
        'bodyweight' => 'Poids du corps',
        'dumbbells' => 'Haltères',
        'barbell' => 'Barre',
        'kettlebell' => 'Kettlebell',
        'band' => 'Élastique',
        'machine' => 'Machine',
        'bench' => 'Banc',
    ];

    /**
     * @param  list<string>|null  $keys
     * @param  array<string, string>  $catalog
     * @return list<string>
     */
    public static function labels(?array $keys, array $catalog): array
    {
        if ($keys === null || $keys === []) {
            return [];
        }

        $labels = [];

        foreach ($keys as $key) {
            if (isset($catalog[$key])) {
                $labels[] = $catalog[$key];
            }
        }

        return $labels;
    }
}
