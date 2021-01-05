<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Video;

class VideoChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function join(User $user, Video $model): bool
    {
        return true;
    }
}
