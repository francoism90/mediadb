<?php

namespace App\Console\Commands\Media;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:import {path} {user} {collection=videos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import media file(s) to the user';

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
    public function handle()
    {
        $user = User::findOrFail($this->argument('user'));

        foreach ($this->getPathFiles() as $file) {
            $this->info("Importing {$file->getFilename()}");

            $media = $user
                ->addMedia($file->getRealPath())
                ->usingName($file->getFilename())
                ->toMediaCollection($this->argument('collection'));

            $media->setStatus('private', 'needs approval');
        }
    }

    /**
     * @return Finder
     */
    protected function getPathFiles(): Finder
    {
        // Make sure to only import valid media
        $filter = function (\SplFileInfo $file) {
            $mime = mime_content_type($file->getRealPath());

            return in_array($mime, $this->supportedMimeTypes());
        };

        return (new Finder())
            ->files()
            ->in($this->argument('path'))
            ->ignoreDotFiles(true)
            ->depth('== 0')
            ->name($this->supportedFileNames())
            ->filter($filter)
            ->sortByName();
    }

    /**
     * @return array
     */
    protected function supportedFileNames(): array
    {
        return [
            '*.mp4', '*.m4v', '*.webm',
            '*.ogg', '*.ogm', '*.ogv',
        ];
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return [
            'video/mp4', 'video/x-m4v', 'video/mp4v-es',
            'video/x-ogg', 'video/x-ogm', 'video/x-ogm+ogg',
            'video/x-theora', 'video/x-theora+ogg', 'video/webm',
        ];
    }
}
