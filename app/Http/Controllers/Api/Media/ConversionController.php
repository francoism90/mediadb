<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConversionController extends Controller
{
    public function __construct(
        protected DefaultPathGenerator $basePathGenerator,
        protected FilesystemManager $filesystemManager
    ) {
    }

    public function __invoke(Media $media, ?User $user, string $name): BinaryFileResponse
    {
        abort_if(!$user || !$user->hasAnyRole(['member', 'super-admin']), 403);

        // We need to define conversions
        $conversions = collect([
            ['name' => 'thumbnail', 'path' => config('media.thumbnail_name')],
        ]);

        $conversion = $conversions->firstWhere('name', $name);

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
