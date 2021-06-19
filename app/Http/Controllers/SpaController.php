<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class SpaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json();
    }
}
