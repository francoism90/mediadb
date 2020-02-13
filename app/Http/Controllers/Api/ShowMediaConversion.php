<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class ShowMediaConversion extends Controller
{
    /**
     * @param Media  $media
     * @param User   $user
     * @param string $conversion
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user, string $conversion = '')
    {
        $root = dirname($media->getPath());

        switch ($conversion) {
            case 'preview':
                $basename = pathinfo($media->file_name, PATHINFO_FILENAME)."-{$conversion}.mp4";
                $path = "{$root}/conversions/{$basename}";
                break;
            default:
                $path = $media->getPath($conversion);
        }

        if (!$path || !file_exists($path)) {
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
