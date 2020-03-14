<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @param Request $request
     *
     * @return void
     */
    public function me(Request $request)
    {
        return new UserResource(
            $request->user()
        );
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function logout(Request $request)
    {
        return Auth::guard('web')->logout($request);
    }
}
