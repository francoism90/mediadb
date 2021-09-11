<?php

namespace App\Actions\Video;

use App\Actions\Media\ImportToMediaLibrary;
use App\Models\User;
use Symfony\Component\Finder\Finder;

class BulkImportVideos
{
    public function execute(User $user, string $path): void
    {
        $files = $this->gatherFiles($path);

        foreach ($files as $file) {
            $video = app(CreateUserVideo::class)->execute($user, $file);

            app(ImportToMediaLibrary::class)->execute($video, $file, 'clips');
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
