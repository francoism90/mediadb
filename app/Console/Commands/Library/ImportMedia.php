<?php

namespace App\Console\Commands\Library;

use App\Models\Media;
use App\Models\Video;
use App\Services\LibraryService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ImportMedia extends Command
{
    /**
     * @var string
     */
    protected $signature = 'library:import-media {path} {id} {type=video} {collection=subtitles}';

    /**
     * @var string
     */
    protected $description = 'Import media files to a library model';

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
        $model = $this->getModel();

        $files = $libraryService->getFilesInPath(
            $this->argument('path')
        );

        foreach ($files as $file) {
            $this->info("Importing {$file->getFilename()}");

            $libraryService->import($model, $file, $this->argument('collection'));
        }
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        switch ($this->argument('type')) {
            case 'media':
                return Media::findOrFail($this->argument('id'));
                break;
            default:
                return Video::findOrFail($this->argument('id'));
        }
    }
}
