<?php

namespace App\Broadcasting;

use App\Models\Tag;
use App\Models\User;

class TagChannel
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
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $video
     *
     * @return array|bool
     */
    public function join(User $user, Tag $tag)
    {
        return true;
    }
}
