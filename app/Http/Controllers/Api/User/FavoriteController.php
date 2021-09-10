<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\User\MarkModelAsFavorite;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\FavoriteRequest;
use App\Http\Resources\ModelResource;
use Illuminate\Database\Eloquent\Model;

class FavoriteController extends Controller
{
    public function __invoke(Model $model, FavoriteRequest $request): ModelResource
    {
        app(MarkModelAsFavorite::class)->execute(
            auth()->user(),
            $model,
            $request->boolean('favorite')
        );

        $model->refresh();

        return new ModelResource(
            $model->append('favorite')
        );
    }
}
