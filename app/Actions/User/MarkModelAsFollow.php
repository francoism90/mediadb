<?php

namespace App\Actions\User;

use App\Models\User;
use App\Notifications\FollowingModel;
use Illuminate\Database\Eloquent\Model;

class MarkModelAsFollow
{
    public function execute(User $user, Model $model, bool $force = false): void
    {
        throw_if(!method_exists($model, 'followers'));

        $force
            ? $user->follow($model)
            : $user->toggleFollow($model);

        $user->notify(new FollowingModel($model));
    }
}
