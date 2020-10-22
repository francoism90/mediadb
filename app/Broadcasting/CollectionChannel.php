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
     * @param \App\Models\Collection $collection
     *
     * @return array|bool
     */
    public function join(User $user, Collection $collection)
    {
        // TODO: check user role(s)

        return true;
    }
}
