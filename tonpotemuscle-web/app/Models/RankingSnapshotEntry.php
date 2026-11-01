<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ranking_snapshot_id',
    'user_id',
    'rank',
    'points',
    'live_count',
    'username',
])]
class RankingSnapshotEntry extends Model
{
    /**
     * @return BelongsTo<RankingSnapshot, $this>
     */
    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(RankingSnapshot::class, 'ranking_snapshot_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
