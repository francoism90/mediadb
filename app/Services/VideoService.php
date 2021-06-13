<?php

namespace App\Services;

use App\Console\Commands\Video\ImportCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class VideoService
{
    public function __construct(
        protected MediaSyncService $syncService
    ) {
    }

    /**
     * @param Model              $model
     * @param string|null        $path
     * @param string|null        $collection
     * @param ImportCommand|null $command
     *
     * @return void
     */
    public function import(
        Model $model,
        ?string $path = null,
        ?string $collection = null,
        ?ImportCommand $command = null
    ): void {
        $results = [
            'success' => [],
            'bad_files' => [],
        ];

        $path = $path ?: Storage::disk('import')->path('');

        $collection = $collection ?: config('video.default_collection', 'clip');

        $files = $this->syncService->gatherFiles($path);

        if ($command) {
            $command->createProgressBar(count($files));
        }

        foreach ($files as $file) {
            $filePath = $file->getRealPath();

            try {
                $baseModel = $model->videos()->create([
                    'name' => Str::title($file->getFilenameWithoutExtension()),
                ]);

                $this->syncService->add($baseModel, $file, $collection);

                $results['success'][] = $filePath;
            } catch (Throwable $e) {
                report($e);

                $results['bad_files'][] = $filePath;
            }

            if ($command) {
                $command->advanceProgressBar();
                $command->logStatusToConsole($results);
            }
        }
    }
}
