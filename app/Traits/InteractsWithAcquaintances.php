<?php

namespace App\Traits;

use App\Models\User;

trait InteractsWithAcquaintances
{
    /**
     * @return bool
     */
    public function getIsFavoritedAttribute(?User $user = null): bool
    {
        return $this->isFavoritedBy(
            $user ?? auth()->user()
        );
    }

    /**
     * @return int
     */
    public function getFavoritesAttribute(): int
    {
        return $this->favoritersCount();
    }

    /**
     * @return bool
     */
    public function getIsLikedAttribute(?User $user = null): bool
    {
        return $this->isLikedBy(
            $user ?? auth()->user()
        );
    }

    /**
     * @return int
     */
    public function getLikedAttribute(): int
    {
        return $this->likedCount();
    }

    /**
     * @return bool
     */
    public function getIsSubscribedAttribute(?User $user = null): bool
    {
        return $this->isSubscribedBy(
            $user ?? auth()->user()
        );
    }

    /**
     * @return int
     */
    public function getSubscribersAttribute(): int
    {
        return $this->subscribersCount();
    }
}
