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
    'description',
])]
class Bilan extends Model
{
    /** @use HasFactory<BilanFactory> */
    use HasFactory, HasUuids, SoftDeletes;

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
