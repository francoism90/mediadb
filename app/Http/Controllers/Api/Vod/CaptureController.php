<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VodService;
use Illuminate\Http\JsonResponse;

class CaptureController extends Controller
{
    public function __invoke(Video $video, int $offset): JsonResponse
    {
        $uri = sprintf('thumb-%s-w160-h90.jpg', $offset * 1000);

        $url = app(VodService::class)->generateUrl('thumb', $uri, [
            'video' => $video,
        ]);

        return response()->json([
            'thumb_url' => $url,
            'offset' => $offset,
        ]);
    }
}
