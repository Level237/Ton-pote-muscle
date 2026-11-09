<?php

namespace App\Models\Concerns;

use App\Support\ShareToken;

trait HasShareToken
{
    public function ensureShareToken(): string
    {
        if ($this->share_token) {
            return $this->share_token;
        }

        $this->forceFill(['share_token' => ShareToken::unique()])->save();

        return $this->share_token;
    }

    public function shareUrl(): string
    {
        return route('share.show', $this->ensureShareToken());
    }
}
