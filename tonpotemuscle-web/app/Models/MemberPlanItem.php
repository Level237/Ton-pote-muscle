<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use App\Support\MemberEquipment;
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
    'loads',
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
            'loads' => 'array',
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

    public function isCatchUp(): bool
    {
        return ! $this->isRest()
            && $this->planned_on !== null
            && $this->planned_on->lt(now()->startOfDay());
    }

    public function isProgramClosed(): bool
    {
        $program = $this->assignment?->program;

        if ($program === null) {
            return false;
        }

        if ($program->status === ProgramStatus::Closed || $program->closed_at !== null) {
            return true;
        }

        return $program->ends_on !== null && $program->ends_on->lt(now()->startOfDay());
    }

    public function label(): string
    {
        if ($this->isRest()) {
            return 'Repos';
        }

        return $this->workout?->title ?? 'Séance';
    }

    public function loadKg(WorkoutExercise $row): ?float
    {
        $value = ($this->loads ?? [])[(string) $row->id] ?? ($this->loads ?? [])[$row->id] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    public function loadLabel(WorkoutExercise $row): string
    {
        return $row->workLabel($this->loadKg($row));
    }

    public function loadDisplay(WorkoutExercise $row): ?string
    {
        return MemberEquipment::formatLoad($this->loadKg($row));
    }
}
