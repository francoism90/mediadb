<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function __invoke(Media $media, User $user): BinaryFileResponse | NotFoundException
    {
        $path = $media->getPath();

        abort_if(!$path || !file_exists($path), 404);

        return response()->download(
            $path,
            $media->file_name,
            [
                'Content-Type' => $media->mime_type,
            ]
        );
    }
}
