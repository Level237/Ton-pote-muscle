<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'bilan_id',
    'submitted_at',
])]
class BilanSubmission extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
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
     * @return BelongsTo<Bilan, $this>
     */
    public function bilan(): BelongsTo
    {
        return $this->belongsTo(Bilan::class);
    }

    /**
     * @return HasMany<BilanAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(BilanAnswer::class);
    }

    /**
     * @return array<int, string>
     */
    public function valuesByQuestion(): array
    {
        return $this->answers
            ->mapWithKeys(fn (BilanAnswer $answer) => [
                $answer->bilan_question_id => $answer->value,
            ])
            ->all();
    }
}
