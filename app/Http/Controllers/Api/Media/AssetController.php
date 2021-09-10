<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function __invoke(Media $media, string $conversion): BinaryFileResponse
    {
        abort_if(!$media->hasGeneratedConversion($conversion), 404);

        $absolutePath = $media->getPath($conversion);

        return response()->download(
            $absolutePath,
            basename($absolutePath)
        );
    }
}
