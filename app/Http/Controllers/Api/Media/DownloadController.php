<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * @param Media $media
     * @param User  $user
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user): BinaryFileResponse | NotFoundException
    {
        $path = $media->getPath();

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $media->file_name, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
