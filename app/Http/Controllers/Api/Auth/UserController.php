<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return UserResource
     */
    public function __invoke(Request $request): JsonResponse
    {
        app()->setLocale($request->user()->preferredLocale());

        $user = $request->user()->append([
            'assigned_permissions',
            'assigned_roles',
            'avatar_url',
            'settings',
        ]);

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
