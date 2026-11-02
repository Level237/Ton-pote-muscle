<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bilan_submission_id',
    'bilan_question_id',
    'value',
])]
class BilanAnswer extends Model
{
    /**
     * @return BelongsTo<BilanSubmission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(BilanSubmission::class, 'bilan_submission_id');
    }

    /**
     * @return BelongsTo<BilanQuestion, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(BilanQuestion::class, 'bilan_question_id');
    }
}
