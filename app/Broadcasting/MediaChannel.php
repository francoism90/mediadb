<?php

namespace App\Broadcasting;

use App\Models\Media;
use App\Models\User;

class MediaChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function join(User $user, Media $model): bool
    {
        return true;
    }
}
