<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use App\Support\WorkoutCatalog;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
     * @return HasMany<ProgramWorkout, $this>
     */
    public function schedule(): HasMany
    {
        return $this->hasMany(ProgramWorkout::class)->orderBy('day_number');
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

    public function isPublic(): bool
    {
        return $this->status !== ProgramStatus::Draft;
    }

    public function levelLabel(): ?string
    {
        if (! $this->level) {
            return null;
        }

        return WorkoutCatalog::DIFFICULTIES[$this->level] ?? $this->level;
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function publicUrl(): string
    {
        return route('programs.show', $this);
    }
}
