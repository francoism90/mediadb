<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function __invoke(Request $request)
    {
        $this->login($request);
    }
}
