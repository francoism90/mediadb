<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __invoke(Video $video): JsonResponse
    {
        return response()->json(
            $video->getManifestContents()
        );
    }
}
