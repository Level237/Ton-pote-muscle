<?php

namespace App\Models;

use App\Support\WorkoutCatalog;
use Database\Factories\WorkoutFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'difficulty', 'total_duration_seconds', 'thumbnail_path'])]
class Workout extends Model
{
    /** @use HasFactory<WorkoutFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * @return HasMany<WorkoutExercise, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class)->orderBy('position');
    }

    public function durationMinutes(): int
    {
        return (int) round($this->total_duration_seconds / 60);
    }

    public function difficultyLabel(): ?string
    {
        if (! $this->difficulty) {
            return null;
        }

        return WorkoutCatalog::DIFFICULTIES[$this->difficulty] ?? $this->difficulty;
    }

    public function thumbnailUrl(): ?string
    {
        if (! $this->thumbnail_path) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }
}
