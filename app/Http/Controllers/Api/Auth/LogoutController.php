<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutRequest;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(LogoutRequest $request): JsonResponse
    {
        $statefulDomains = explode(',', env('SANCTUM_STATEFUL_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'), PHP_URL_HOST);

        if (in_array($requestHost, $statefulDomains)) {
            throw_if(
                Auth::guard('web')->logout(),
                AuthorizationException::class,
                'Unable to logout'
            );
        }

        $request->user()->tokens()->where('token', $request->input('token'))->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
