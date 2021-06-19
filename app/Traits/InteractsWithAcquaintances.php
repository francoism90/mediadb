<?php

namespace App\Traits;

use App\Models\User;

trait InteractsWithAcquaintances
{
    public function getFavoriteAttribute(?User $user = null): bool
    {
        return $this->isFavoritedBy(
            $user ?? auth()->user()
        );
    }

    public function getSubscribedAttribute(?User $user = null): bool
    {
        return $this->isSubscribedBy(
            $user ?? auth()->user()
        );
    }
}
