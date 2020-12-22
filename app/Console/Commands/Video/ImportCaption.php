<?php

namespace App\Console\Commands\Video;

use App\Models\Video;
use App\Services\MediaImportService;
use Illuminate\Console\Command;

class ImportCaption extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import-caption {path} {id} {collection=caption} {locale=en}';

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
    public function handle(MediaImportService $mediaImportService): void
    {
        $files = $mediaImportService->getValidFiles(
            $this->argument('path')
        );

        $baseModel = $this->getBaseModel();

        foreach ($files as $file) {
            $this->info("Importing {$file->getFilename()}");

            $mediaImportService->import(
                $baseModel,
                $file,
                $this->argument('collection'),
                ['locale' => $this->argument('locale')]
            );
        }
    }

    /**
     * @return Video
     */
    protected function getBaseModel(): Video
    {
        return Video::findOrFail(
            $this->argument('id')
        );
    }
}
