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
        protected SyncService $syncService
    ) {
    }

    public function import(
        Model $model,
        ?string $path = null,
        ?string $collection = null,
        ?ImportCommand $command = null
    ): void {
        $results = collect();

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

                $results->push([
                    'path' => $filePath,
                    'success' => true,
                ]);
            } catch (Throwable $e) {
                report($e);

                $results->push([
                    'path' => $filePath,
                    'success' => false,
                ]);
            }

            if ($command) {
                $command->advanceProgressBar();
                $command->logStatusToConsole($results);
            }
        }
    }
}
