<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FollowRequest;
use App\Http\Resources\ModelResource;
use App\Notifications\FollowModel;
use Illuminate\Database\Eloquent\Model;

class FollowController extends Controller
{
    public function __invoke(Model $model, FollowRequest $request): ModelResource
    {
        throw_if(!method_exists($model, 'followers'));

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->boolean('follow')
            ? $user->follow($model)
            : $user->toggleFollow($model);

        $model->refresh();
        $user->notify(new FollowModel($model));

        return new ModelResource($model);
    }
}
