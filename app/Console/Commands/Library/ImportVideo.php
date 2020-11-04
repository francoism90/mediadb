<?php

namespace App\Console\Commands\Library;

use App\Models\User;
use App\Models\Video;
use App\Services\LibraryService;
use Illuminate\Console\Command;

class ImportVideo extends Command
{
    /**
     * @var string
     */
    protected $signature = 'library:import-video {path} {collection=clips} {user=1}';

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

            $model = $this->createModel([
                'name' => $file->getFilenameWithoutExtension(),
            ]);

            $libraryService->import($model, $file, $this->argument('collection'));
        }
    }

    /**
     * @param array $attributes
     *
     * @return Video
     */
    protected function createModel(array $attributes): Video
    {
        return $this->getUser()
            ->videos()
            ->create($attributes);
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return User::findOrFail(
            $this->argument('user')
        );
    }
}
