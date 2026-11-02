<?php

namespace App\Models;

use Database\Factories\BilanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'slug',
    'description',
])]
class Bilan extends Model
{
    public const SLUG_DEPARTURE = 'depart';

    /** @use HasFactory<BilanFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public static function departure(): ?self
    {
        return static::query()->where('slug', self::SLUG_DEPARTURE)->first();
    }

    public function isDeparture(): bool
    {
        return $this->slug === self::SLUG_DEPARTURE;
    }

    /**
     * @return HasMany<BilanQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(BilanQuestion::class)->orderBy('position');
    }

    /**
     * @return HasMany<BilanSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(BilanSubmission::class);
    }
}
