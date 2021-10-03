<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\User\DeleteUserToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutRequest;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __invoke(LogoutRequest $request): JsonResponse
    {
        app(DeleteUserToken::class)(
            $request->user(),
            $request->input('token', '')
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
