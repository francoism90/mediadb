<?php

namespace App\Actions\User;

use App\Models\User;
use App\Notifications\FavoritedModel;
use Illuminate\Database\Eloquent\Model;

class MarkModelAsFavorite
{
    public function execute(User $user, Model $model, bool $force = false): void
    {
        throw_if(!method_exists($model, 'favoriters'));

        $force
            ? $user->favorite($model)
            : $user->toggleFavorite($model);

        $user->notify(new FavoritedModel($model));
    }
}
