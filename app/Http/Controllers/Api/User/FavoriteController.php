<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use App\Notifications\FavoriteModel;
use Illuminate\Database\Eloquent\Model;

class FavoriteController extends Controller
{
    public function __invoke(Model $model): ModelResource
    {
        throw_if(!method_exists($model, 'favoriters'));

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->toggleFavorite($model);

        $model->refresh();
        $user->notify(new FavoriteModel($model));

        return new ModelResource($model);
    }
}
