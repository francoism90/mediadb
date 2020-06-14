<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class PlaceholderController extends Controller
{
    /**
     * @param Media $media
     * @param User  $user
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user)
    {
        $path = $media->getPath('thumbnail');

        if (!$path || !file_exists($path)) {
            abort(404);
        }

        $type = mime_content_type($path);

        // Internal redirect path
        $root = dirname($path);
        $asset = str_replace($root, '', $path);

        header("X-Assets-Root: {$root}");
        header("X-Accel-Redirect: /assets/{$asset}");
        header('Content-Disposition: attachment; filename="'.basename($path));
        header("Content-Type: {$type}");

        exit;
    }
}
