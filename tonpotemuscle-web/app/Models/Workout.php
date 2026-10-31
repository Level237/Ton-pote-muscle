<?php

namespace App\Models;

use Database\Factories\WorkoutFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'difficulty', 'total_duration_seconds', 'thumbnail_path'])]
class Workout extends Model
{
    /** @use HasFactory<WorkoutFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public function durationMinutes(): int
    {
        return max(1, (int) round($this->total_duration_seconds / 60));
    }
}
