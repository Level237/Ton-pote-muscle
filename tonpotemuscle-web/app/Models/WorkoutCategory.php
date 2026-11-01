<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title'])]
class WorkoutCategory extends Model
{
    use HasUuids;

    /**
     * @return HasMany<Workout, $this>
     */
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'category_id');
    }
}
