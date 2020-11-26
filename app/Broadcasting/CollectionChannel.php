<?php

namespace App\Broadcasting;

use App\Models\Collection;
use App\Models\User;

class CollectionChannel
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
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $model
     *
     * @return array|bool
     */
    public function join(User $user, Collection $model)
    {
        return true;
    }
}
