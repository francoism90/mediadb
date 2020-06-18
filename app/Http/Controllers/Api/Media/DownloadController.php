<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class DownloadController extends Controller
{
    /**
     * @param Media  $media
     * @param User   $user
     * @param string $conversion
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user, ?string $conversion = '')
    {
        $conversion = 'media' === $conversion ? '' : $conversion;

        if ($conversion && !$media->hasGeneratedConversion($conversion)) {
            abort(404);
        }

        if (!$conversion && !$user->hasRole('super-admin')) {
            abort(403);
        }

        $path = $media->getPath($conversion);
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
