<?php

namespace App\Models;

use App\Support\MemberWeight;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'week_start',
    'weighed_on',
    'weight_kg',
])]
class WeightLog extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'week_start' => 'date:Y-m-d',
            'weighed_on' => 'date:Y-m-d',
            'weight_kg' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function formatted(): string
    {
        return MemberWeight::format($this->weight_kg);
    }
}
