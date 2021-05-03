<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FavoriteRequest;
use App\Http\Resources\ModelResource;
use App\Notifications\FavoriteModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\PrefixedIds\PrefixedIds;

class FavoriteController extends Controller
{
    /**
     * @param FavoriteRequest $request
     *
     * @return ModelResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(FavoriteRequest $request): ModelResource | JsonResource
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $model = PrefixedIds::find($request->input('id'));

        throw_if(!$model, ModelNotFoundException::class);

        $user->toggleFavorite($model);
        $model->refresh();

        $user->notify(new FavoriteModel($model));

        return new ModelResource($model);
    }
}
