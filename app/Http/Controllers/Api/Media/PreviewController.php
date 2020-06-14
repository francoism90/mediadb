<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class PreviewController extends Controller
{
    /**
     * @param Media $media
     * @param User  $user
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user)
    {
        $root = dirname($media->getPath());

        $basename = pathinfo($media->file_name, PATHINFO_FILENAME).'-preview.mp4';
        $path = "{$root}/conversions/{$basename}";

        if (!file_exists($path)) {
            abort(404);
        }

        $type = mime_content_type($path);

        // Internal redirect path
        $asset = str_replace($root, '', $path);

        header("X-Assets-Root: {$root}");
        header("X-Accel-Redirect: /assets/{$asset}");
        header('Content-Disposition: attachment; filename="'.basename($path));
        header("Content-Type: {$type}");

        exit;
    }
}
