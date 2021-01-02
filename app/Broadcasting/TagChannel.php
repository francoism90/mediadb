<?php

namespace App\Broadcasting;

use App\Models\Tag;
use App\Models\User;

class TagChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param Tag  $model
     *
     * @return array|bool
     */
    public function join(User $user, Tag $model): bool
    {
        return true;
    }
}
