<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PublicFile
{
    public static function url(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
