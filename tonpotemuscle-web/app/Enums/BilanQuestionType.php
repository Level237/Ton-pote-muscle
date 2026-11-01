<?php

namespace App\Enums;

enum BilanQuestionType: string
{
    case Text = 'text';
    case Number = 'number';
    case Choice = 'choice';
    case Rating = 'rating';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Texte',
            self::Number => 'Nombre',
            self::Choice => 'Choix',
            self::Rating => 'Note (1 à 5)',
        };
    }
}
