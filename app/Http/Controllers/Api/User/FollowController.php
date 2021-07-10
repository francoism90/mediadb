<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FollowRequest;
use App\Http\Resources\ModelResource;
use App\Notifications\FollowModel;
use Spatie\PrefixedIds\PrefixedIds;

class FollowController extends Controller
{
    public function __invoke(FollowRequest $request): ModelResource
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $model = PrefixedIds::find($request->input('id'));

        abort_if(!$model, 404);

        $user->toggleFollow($model);

        $model->refresh();
        $user->notify(new FollowModel($model));

        return new ModelResource($model);
    }
}
