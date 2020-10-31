<?php

namespace App\Console\Commands\Library;

use App\Models\Media;
use App\Models\Video;
use App\Services\LibraryService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class Import extends Command
{
    /**
     * @var string
     */
    protected $signature = 'library:import {path} {id} {type=video} {collection=clips}';

    /**
     * @var string
     */
    protected $description = 'Import files to a library model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle(LibraryService $libraryService): void
    {
        $model = $this->findModel();
        $collection = $this->argument('collection');

        $this->info("{$model->name} ({$this->argument('type')})");
        $this->newLine();

        // Import valid files from given path
        $files = $libraryService->getFilesInPath(
            $this->argument('path')
        );

        foreach ($files as $file) {
            $this->info("Importing {$file->getFilename()}");

            $libraryService->import($model, $file, $collection);
        }
    }

    /**
     * @return Model
     */
    protected function findModel(): Model
    {
        switch ($this->argument('type')) {
            case 'media':
                return Media::findOrFail($this->argument('id'));
            default:
                return Video::findOrFail($this->argument('id'));
        }
    }
}
