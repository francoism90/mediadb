<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaStreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ThumbnailController extends Controller
{
    public function __construct(
        protected MediaStreamService $mediaStreamService
    ) {
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return JsonResponse
     */
    public function __invoke(Media $media, float $offset): JsonResponse
    {
        $thumbnailUrl = $this
            ->mediaStreamService
            ->getMappingUrl('thumb', 'thumb-1000-w150-h100.jpg', [
                'media' => $media,
                'user' => auth()->user()
            ]);

        logger($thumbnailUrl);

        return response()->json([
            'offset' => $offset,
            'url' => $thumbnailUrl
        ]);
    }
}
