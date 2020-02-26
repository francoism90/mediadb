<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;

class ThumbnailController extends Controller
{
    /**
     * @param Media $media
     * @param int   $offset
     *
     * @return MediaResource
     */
    public function __invoke(Media $media, int $offset = 1000)
    {
        return (new MediaResource($media))
            ->additional([
                'meta' => [
                    'thumbnail' => $media->getStreamThumbUrlAttribute($offset),
                    'offset' => $offset,
                ],
            ]);
    }
}
