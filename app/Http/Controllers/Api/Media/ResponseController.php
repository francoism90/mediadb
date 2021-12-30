<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseController extends Controller
{
    public function __invoke(Request $request, Media $media): BinaryFileResponse
    {
        // Media Conversion
        $conversion = $request->input('conversion', '');

        abort_if($conversion && !$media->hasGeneratedConversion($conversion), 404);

        // Generate response
        $path = $media->getPath($conversion);

        return response()->file($path);
    }
}
