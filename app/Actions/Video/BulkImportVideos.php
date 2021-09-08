<?php

namespace App\Actions\Video;

use App\Actions\Media\ImportToMediaLibrary;
use App\Models\User;
use Symfony\Component\Finder\Finder;

class BulkImportVideos
{
    public function __construct(
        protected CreateNewVideo $createNewVideo,
        protected ImportToMediaLibrary $importToMediaLibrary,
    ) {
    }

    public function execute(User $user, string $path): void
    {
        $files = $this->gatherFiles($path);

        foreach ($files as $file) {
            $model = $this->createNewVideo->execute($user, $file);

            $this->importToMediaLibrary->execute($model, $file, 'clip');
        }
    }

    private function gatherFiles(string $path): Finder
    {
        return (new Finder())
            ->files()
            ->followLinks()
            ->sortByName()
            ->name(config('api.import.video_extensions'))
            ->in($path);
    }
}
