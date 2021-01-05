<?php

namespace App\Broadcasting;

use App\Models\Collection;
use App\Models\User;

class CollectionChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User       $user
     * @param Collection $model
     *
     * @return bool
     */
    public function join(User $user, Collection $model): bool
    {
        return true;
    }
}
