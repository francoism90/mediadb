<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FavoriteRequest;
use App\Http\Resources\ModelResource;
use App\Notifications\FavoriteModel;
use Illuminate\Database\Eloquent\Model;

class FavoriteController extends Controller
{
    public function __invoke(Model $model, FavoriteRequest $request): ModelResource
    {
        throw_if(!method_exists($model, 'favoriters'));

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->boolean('favorite')
            ? $user->favorite($model)
            : $user->toggleFavorite($model);

        $model->refresh();
        $user->notify(new FavoriteModel($model));

        return new ModelResource(
            $model->append('favorite')
        );
    }
}
