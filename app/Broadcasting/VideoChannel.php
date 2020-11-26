<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Video;

class VideoChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $model
     *
     * @return array|bool
     */
    public function join(User $user, Video $model)
    {
        return true;
    }
}
