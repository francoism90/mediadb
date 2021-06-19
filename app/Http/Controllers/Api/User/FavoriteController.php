<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FavoriteRequest;
use App\Http\Resources\ModelResource;
use App\Notifications\FavoriteModel;
use Spatie\PrefixedIds\PrefixedIds;

class FavoriteController extends Controller
{
    public function __invoke(FavoriteRequest $request): ModelResource
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $model = PrefixedIds::find($request->input('id'));

        abort_if(!$model, 404);

        $user->toggleFavorite($model);

        $model->refresh();

        $user->notify(new FavoriteModel($model));

        return new ModelResource($model);
    }
}
