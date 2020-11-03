<?php

namespace App\Console\Commands\Library;

use App\Models\User;
use App\Services\LibraryService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ImportVideo extends Command
{
    /**
     * @var string
     */
    protected $signature = 'library:import-video {path} {id?} {collection=clips} {user=1}';

    /**
     * @var string
     */
    protected $description = 'Import video files to the library';

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
        $files = $libraryService->getFilesInPath(
            $this->argument('path')
        );

        foreach ($files as $file) {
            $this->info("Importing {$file->getFilename()}");

            $model = $this->firstOrCreateModel(
                ['id' => $this->argument('id')],
                ['name' => $file->getFilenameWithoutExtension()]
            );

            $libraryService->import($model, $file, $this->argument('collection'));
        }
    }

    /**
     * @param array|null $attributes
     * @param array|null $values
     *
     * @return Model
     */
    protected function firstOrCreateModel(?array $attributes = [], ?array $values = []): Model
    {
        $user = User::findOrFail(
            $this->argument('user')
        );

        return $user
            ->videos()
            ->firstOrCreate($attributes, $values);
    }
}
