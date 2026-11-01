<?php

namespace App\Models;

use App\Enums\BilanQuestionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bilan_id',
    'position',
    'title',
    'type',
    'options',
])]
class BilanQuestion extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => BilanQuestionType::class,
            'options' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Bilan, $this>
     */
    public function bilan(): BelongsTo
    {
        return $this->belongsTo(Bilan::class);
    }
}
