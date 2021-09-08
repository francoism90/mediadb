<?php

namespace App\Services;

use App\Actions\Media\ImportToMediaLibrary;
use App\Actions\User\CreateNewVideo;
use App\Console\Commands\Video\ImportCommand;
use App\Models\User;
use Symfony\Component\Finder\Finder;
use Throwable;

class VideoService
{
    public function __construct(
        protected CreateNewVideo $createNewVideo,
        protected ImportToMediaLibrary $importToMediaLibrary
    ) {
    }

    public function import(
        User $user,
        string $path,
        ?ImportCommand $command = null
    ): void {
        $results = collect();

        $files = $this->gatherFiles($path);

        if (null !== $command) {
            $command->createProgressBar(count($files));
        }

        foreach ($files as $file) {
            $filePath = $file->getRealPath();

            try {
                $model = $this->createNewVideo->execute($user, $file);

                $this->importToMediaLibrary->execute($model, $file, 'clip');

                $results->push([
                    'path' => $filePath,
                    'success' => true,
                ]);
            } catch (Throwable $throwable) {
                report($throwable);

                $results->push([
                    'path' => $filePath,
                    'success' => false,
                ]);
            }

            if (null !== $command) {
                $command->advanceProgressBar();
                $command->logStatusToConsole($results);
            }
        }
    }

    public function gatherFiles(string $path): Finder
    {
        return (new Finder())
            ->files()
            ->followLinks()
            ->sortByName()
            ->name(config('api.sync.extensions'))
            ->in($path);
    }
}
