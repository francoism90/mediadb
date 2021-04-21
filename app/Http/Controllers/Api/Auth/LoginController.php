<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param LoginRequest $request
     *
     * @return AuthResource
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        // Validate login
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        // Session Guard
        $statefulDomains = explode(',', env('SANCTUM_STATEFUL_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'), PHP_URL_HOST);

        if (in_array($requestHost, $statefulDomains)) {
            $credentials = $request->only('email', 'password');

            throw_if(
                !Auth::attempt($credentials, $request->input('remember_me', true)),
                AuthorizationException::class,
                'Unable to authenticate'
            );
        }

        $token = $user->createToken($request->input('device_name'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
