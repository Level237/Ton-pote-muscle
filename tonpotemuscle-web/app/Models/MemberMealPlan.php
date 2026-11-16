<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'assigned_by',
    'breakfast',
    'lunch',
    'dinner',
])]
class MemberMealPlan extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function hasAny(): bool
    {
        return filled($this->breakfast) || filled($this->lunch) || filled($this->dinner);
    }

    public function titleFor(string $slot): ?string
    {
        $value = $this->{$slot} ?? null;

        return filled($value) ? $value : null;
    }
}
