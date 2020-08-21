<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return UserResource
     */
    public function __invoke(Request $request): UserResource
    {
        return new UserResource(
            $request
                ->user()
                ->append([
                    'assigned_roles',
                ])
        );
    }
}
