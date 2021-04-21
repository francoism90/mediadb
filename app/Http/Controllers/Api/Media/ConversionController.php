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

    /**
     * @param Media     $media
     * @param User|null $user
     * @param string    $name
     *
     * @return void
     */
    public function __invoke(Media $media, ?User $user, string $name): BinaryFileResponse
    {
        // Only allow members
        if (!$user || !$user->hasAnyRole(['member', 'super-admin'])) {
            abort(403);
        }

        // We need to use fixed conversions
        $conversions = collect([
            ['name' => 'thumbnail', 'path' => config('media.thumbnail_name')],
        ]);

        $conversion = $conversions->firstWhere('name', $name);

        // Make sure the conversion exists
        if (!$conversion || !$media->hasGeneratedConversion($name)) {
            abort(404);
        }

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
