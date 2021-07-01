<?php

namespace App\Broadcasting;

use App\Models\User;

class UserChannel
{
    public function join(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
