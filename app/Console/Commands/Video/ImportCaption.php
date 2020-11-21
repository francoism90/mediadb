<?php

namespace App\Console\Commands\Video;

use App\Models\Video;
use App\Services\LibraryService;
use Illuminate\Console\Command;

class ImportCaption extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import-caption {path} {collection=caption} {locale=en}';

    /**
     * @var string
     */
    protected $description = 'Import caption files to a video model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(LibraryService $libraryService): void
    {
        $files = $libraryService->getFiles(
            $this->argument('path')
        );

        $model = $this->getVideo();

        foreach ($files as $file) {
            $this->info("Importing {$file->getFilename()}");

            $libraryService->import(
                $model,
                $file,
                $this->argument('collection'),
                ['locale' => $this->argument('locale')]
            );
        }
    }

    /**
     * @return Video
     */
    protected function getVideo(): Video
    {
        return Video::findOrFail(
            $this->argument('id')
        );
    }
}
