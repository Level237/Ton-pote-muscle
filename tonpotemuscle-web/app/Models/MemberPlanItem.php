<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'user_id',
    'member_assignment_id',
    'planned_on',
    'kind',
    'workout_id',
])]
class MemberPlanItem extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $item): void {
            $item->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'planned_on' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<MemberAssignment, $this>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(MemberAssignment::class, 'member_assignment_id');
    }

    /**
     * @return BelongsTo<Workout, $this>
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function isRest(): bool
    {
        return $this->kind === 'rest';
    }

    public function label(): string
    {
        if ($this->isRest()) {
            return 'Repos';
        }

        return $this->workout?->title ?? 'Séance';
    }
}
