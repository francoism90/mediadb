<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function __construct(
        protected DefaultPathGenerator $basePathGenerator,
        protected FilesystemManager $filesystemManager
    ) {
    }

    public function __invoke(Media $media, string $name): BinaryFileResponse
    {
        $conversion = collect(config('api.video_conversions'))
            ->first(fn ($value, $key) => $key === $name);

        abort_if(!$conversion || !$media->hasGeneratedConversion($name), 404);

        $conversionRelativePath = $this
            ->basePathGenerator
            ->getPathForConversions($media);

        $absolutePath = $this
            ->filesystemManager
            ->disk($media->conversions_disk)
            ->path($conversionRelativePath.$conversion['path']);

        return response()->download(
            $absolutePath,
            basename($absolutePath)
        );
    }
}
