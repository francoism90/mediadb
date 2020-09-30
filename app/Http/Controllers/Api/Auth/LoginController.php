<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:1|max:32',
            'device_name' => 'required|string',
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        $statefulDomains = explode(',', env('SANCTUM_STATEFUL_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'), PHP_URL_HOST);

        // Use session guard
        if (in_array($requestHost, $statefulDomains)) {
            $credentials = $request->only('email', 'password');

            throw_if(
                !Auth::attempt($credentials, $request->input('remember', true)),
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
