<?php

namespace App\Http\Controllers;

class SpaController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json();
    }
}
