<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        return response()->json([
            'success' => true,
            'message' => __('Welcome to the MediaDB API. Please do not abuse the service.'),
        ]);
    }
}
