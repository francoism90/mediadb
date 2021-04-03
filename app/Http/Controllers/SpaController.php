<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class SpaController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json();
    }
}
