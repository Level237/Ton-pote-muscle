<?php

namespace App\Enums;

enum ProgramStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Open => 'Ouvert',
            self::Closed => 'Clôturé',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'border-white/10 bg-white/5 text-muted',
            self::Open => 'border-primary/30 bg-primary/15 text-primary',
            self::Closed => 'border-white/10 bg-white/5 text-muted',
        };
    }
}
