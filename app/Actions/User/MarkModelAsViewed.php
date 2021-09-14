<?php

namespace App\Actions\User;

use App\Models\User;
use App\Notifications\ViewedModel;
use Illuminate\Database\Eloquent\Model;

class MarkModelAsViewed
{
    public function __invoke(User $user, Model $model, bool $force = false): void
    {
        throw_if(!method_exists($model, 'viewers'));

        $force
            ? $user->view($model)
            : $user->toggleView($model);

        $user->notify(new ViewedModel($model));
    }
}
