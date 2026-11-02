<?php

namespace App\Support;

final class MemberProfile
{
    /**
     * @return array<string, string>
     */
    public const GENDERS = [
        'masculin' => 'Masculin',
        'feminin' => 'Féminin',
    ];

    /**
     * @return array<string, string>
     */
    public const GOALS = [
        'weight-loss' => 'Perte de poids',
        'muscle-gain' => 'Renforcement musculaire',
        'fitness' => 'Remise en forme',
    ];

    /**
     * @return array<string, string>
     */
    public const FITNESS_LEVELS = [
        'new' => 'C’est nouveau pour moi',
        'start-stop' => 'Je commence toujours mais je ne termine jamais',
        'hard' => 'Faire du sport, c’est un calvaire',
        'seasonal' => 'Je suis saisonnier pour des objectifs précis',
        'regular' => 'Je suis assez régulier',
    ];

    /**
     * @return array<string, string>
     */
    public const ACTIVITY_LEVELS = [
        'sedentary' => 'Sédentaire',
        'lightly' => 'Légèrement actif',
        'moderately' => 'Modérément actif',
        'very' => 'Très actif',
    ];

    /**
     * @return array<string, string>
     */
    public const MOTIVATIONS = [
        'attract' => 'Être plus attirant',
        'strength' => 'Gagner en force',
        'confidence' => 'Confiance en soi',
        'health' => 'Améliorer ma santé',
        'weight' => 'Perte de poids',
        'discipline' => 'Développer ma discipline',
        'energy' => 'Retrouver de l’énergie',
        'example' => 'Montrer l’exemple',
    ];
}
