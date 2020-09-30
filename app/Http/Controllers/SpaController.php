<?php

namespace App\Http\Controllers;

class SpaController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json();
    }
}
