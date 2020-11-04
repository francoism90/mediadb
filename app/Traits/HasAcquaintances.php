<?php

namespace App\Traits;

trait HasAcquaintances
{
    /**
     * @return bool
     */
    public function getIsFavoritedAttribute(): bool
    {
        return $this->isFavoritedBy(
            auth()->user()
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
    public function getIsLikedAttribute(): bool
    {
        return $this->isLikedBy(
            auth()->user()
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
    public function getIsSubscribedAttribute(): bool
    {
        return $this->isSubscribedBy(
            auth()->user()
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
