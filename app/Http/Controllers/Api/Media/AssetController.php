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
        $path = $media->getBaseMediaPath()."/conversions/${name}";

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $name);
    }
}
