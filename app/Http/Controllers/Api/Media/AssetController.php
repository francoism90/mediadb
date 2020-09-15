<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class AssetController extends Controller
{
    /**
     * @param Media  $media
     * @param User   $user
     * @param string $name
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user, string $name)
    {
        $root = $media->getBaseMediaPath();

        switch ($name) {
            case 'sprite':
                $path = "{$root}/conversions/sprite.webp";
                break;
            case 'thumbnail':
                $path = "{$root}/conversions/thumbnail.jpg";
                break;
            default:
                abort(403);
        }

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, basename($path));
    }
}
