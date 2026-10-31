<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'description',
    'objective',
    'level',
    'starts_on',
    'ends_on',
    'status',
    'image_path',
    'closed_at',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'status' => ProgramStatus::class,
            'closed_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Program>  $query
     * @return Builder<Program>
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query
            ->where('status', ProgramStatus::Open)
            ->whereDate('starts_on', '<=', now())
            ->whereDate('ends_on', '>=', now());
    }
}
