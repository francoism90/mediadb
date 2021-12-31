<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseController extends Controller
{
    public function __invoke(Request $request, Media $media): StreamedResponse
    {
        // TODO: add some validation?

        return $media->toInlineResponse($request);
    }
}
