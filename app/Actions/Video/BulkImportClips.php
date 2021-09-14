<?php

namespace App\Actions\Video;

use App\Actions\Media\ImportToMediaLibrary;
use App\Models\Video;
use Symfony\Component\Finder\Finder;

class BulkImportClips
{
    public function __invoke(Video $video, string $path): void
    {
        $files = $this->gatherFiles($path);

        foreach ($files as $file) {
            app(ImportToMediaLibrary::class)($video, $file, 'clips');
        }
    }

    protected function gatherFiles(string $path): Finder
    {
        return (new Finder())
            ->files()
            ->followLinks()
            ->sortByName()
            ->name(config('api.video.clips_extensions'))
            ->in($path);
    }
}
