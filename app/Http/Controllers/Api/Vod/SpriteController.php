<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VodService;
use Illuminate\Http\JsonResponse;

class SpriteController extends Controller
{
    public function __invoke(Video $video): JsonResponse
    {
        $contents = app(VodService::class, ['model' => $video])
            ->getManifestContents()
            ->toJson();

        return response()->json($contents);
    }
}
