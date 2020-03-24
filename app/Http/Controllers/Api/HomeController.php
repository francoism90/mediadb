<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * @return response
     */
    public function __invoke()
    {
        return response()->json([
            'success' => true,
            'message' => 'welcome',
        ]);
    }
}
