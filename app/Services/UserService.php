<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserService
{
    public function favorite(
        User $user, Model | Collection $targets,
        ?bool $delete = false
    ): array
    {
        if ($delete) {
            return $user->unfavorite($targets);
        }

        return $user->favorite($targets);
    }
}
