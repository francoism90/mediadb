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
        ?ImportCommand $command = null
    ): void {
        $results = collect();

        $path = $path ?: Storage::disk('import')->path('');

        $files = $this->syncService->gatherFiles($path);

        if (null !== $command) {
            $command->createProgressBar(count($files));
        }

        foreach ($files as $file) {
            $filePath = $file->getRealPath();

            try {
                $baseModel = $model->videos()->create([
                    'name' => Str::title($file->getFilenameWithoutExtension()),
                ]);

                $this->syncService->add($baseModel, $file, 'clip');

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
}
