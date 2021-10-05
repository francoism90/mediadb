<?php

namespace App\Actions\User;

use App\Events\User\ModelHasBeenFavorited;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MarkModelAsFavorite
{
    public function __invoke(User $user, Model $model, bool $force = false): void
    {
        throw_if(!method_exists($model, 'favoriters'));

        $force
            ? $user->favorite($model)
            : $user->toggleFavorite($model);

        ModelHasBeenFavorited::dispatch($user, $model);
    }
}
