<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Models\Media;

class ThumbnailController extends Controller
{
    /**
     * @param Media $media
     * @param int   $offset
     *
     * @return mixed
     */
    public function __invoke(Media $media, int $offset = 1000)
    {
        $url = $media->getStreamThumbUrlAttribute($offset);

        return redirect($url);
    }
}
