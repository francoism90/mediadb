<?php

namespace App\Broadcasting;

use App\Models\User;

class UserChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param User $model
     *
     * @return bool
     */
    public function join(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
