<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VodService;

class CaptureController extends Controller
{
    public function __invoke(Video $video, int $offset): VideoResource
    {
        $uri = sprintf('thumb-%s-w160-h90.jpg', $offset * 1000);

        $url = app(VodService::class)->generateUrl('thumb', $uri, [
            'video' => $video,
        ]);

        return (new VideoResource($video))->additional([
            'data' => [
                'capture_url' => $url,
            ],
        ]);
    }
}
