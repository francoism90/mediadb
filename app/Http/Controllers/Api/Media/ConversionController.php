<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class ConversionController extends Controller
{
    /**
     * @var DefaultPathGenerator
     */
    protected $basePathGenerator;

    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    public function __construct(
        DefaultPathGenerator $basePathGenerator,
        FilesystemManager $filesystemManager
    ) {
        $this->basePathGenerator = $basePathGenerator;
        $this->filesystemManager = $filesystemManager;
    }

    /**
     * @param Media  $media
     * @param User   $user
     * @param string $name
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user, string $name)
    {
        if (!$media->hasGeneratedConversion($name)) {
            abort(404);
        }

        $conversionBasePath = $this->basePathGenerator->getPathForConversions($media);

        $conversionPath = $this->filesystemManager->disk($media->conversions_disk)->path($conversionBasePath);

        $conversions = collect([
            'sprite' => ['path' => config('video.sprite_name')],
            'thumbnail' => ['path' => config('video.thumbnail_name')],
        ]);

        $conversion = $conversions->get($name) ?? abort(501);

        return response()->download($conversionPath.$conversion['path'], $conversion['path']);
    }
}
