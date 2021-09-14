<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\User\MarkModelAsFollow;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\FollowRequest;
use App\Http\Resources\ModelResource;
use Illuminate\Database\Eloquent\Model;

class FollowController extends Controller
{
    public function __invoke(Model $model, FollowRequest $request): ModelResource
    {
        app(MarkModelAsFollow::class)(
            auth()->user(),
            $model,
            $request->boolean('follow')
        );

        $model->refresh();

        return new ModelResource(
            $model->append('following')
        );
    }
}
