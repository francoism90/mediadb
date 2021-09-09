<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __invoke(Video $video): JsonResponse
    {
        logger('manifest');
        logger($video->getManifestContents());
        return response()->json(
            $video->getManifestContents()
        );
    }
}
