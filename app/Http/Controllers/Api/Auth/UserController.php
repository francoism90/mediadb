<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user()->append([
            'assigned_permissions',
            'assigned_roles',
            'avatar_url',
            'email',
            'settings',
        ]);

        return response()->json([
            'token' => $request->bearerToken(),
            'user' => new UserResource($user),
        ]);
    }
}
