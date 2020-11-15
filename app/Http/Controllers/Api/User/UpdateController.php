<?php

namespace App\Http\Controllers\Api\User;

use App\Events\UserHasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\UserResource;

class UpdateController extends Controller
{
    /**
     * @param UpdateRequest $request
     *
     * @return UserResource
     */
    public function __invoke(UpdateRequest $request)
    {
        $user = auth()->user();

        // TODO: add settings sync

        // event(new UserHasBeenUpdated($user));

        return new UserResource($user);
    }
}
