<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function __invoke(Request $request)
    {
        return new UserResource(
            $request->user()
        );
    }
}
