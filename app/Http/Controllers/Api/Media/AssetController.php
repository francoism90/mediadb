<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function __invoke(Media $media, string $conversion = null): BinaryFileResponse
    {
        abort_if(!$media->hasGeneratedConversion($conversion), 404);

        $path = $media->getPath($conversion);

        return response()->download(
            $path,
            basename($path)
        );
    }
}
