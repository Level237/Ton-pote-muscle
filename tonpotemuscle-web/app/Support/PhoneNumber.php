<?php

namespace App\Support;

final class PhoneNumber
{
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $plus = (bool) preg_match('/^\s*\+/', $phone);
        $raw = preg_replace('/\D/', '', $phone) ?? '';

        if ($raw === '') {
            return null;
        }

        return ($plus ? '+' : '').$raw;
    }

    public static function rule(): string
    {
        return 'regex:/^\+?\d{8,15}$/';
    }
}
