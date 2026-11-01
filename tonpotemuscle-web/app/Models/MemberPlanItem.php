<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'member_assignment_id',
    'planned_on',
    'kind',
    'workout_id',
])]
class MemberPlanItem extends Model
{
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
