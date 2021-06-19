<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: actually do refreshing

        return response()->json();
    }
}
