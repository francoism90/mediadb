<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Validate login
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        // Session Guard
        $statefulDomains = explode(',', env('SANCTUM_STATEFUL_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'), PHP_URL_HOST);

        if (in_array($requestHost, $statefulDomains)) {
            $credentials = $request->only('email', 'password');

            throw_if(
                !auth()->attempt($credentials, $request->input('remember', true)),
                AuthorizationException::class,
                'Unable to authenticate'
            );
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()
            ->json(['token' => $token])
            ->header('Authorization', 'Bearer '.$token);
    }
}
