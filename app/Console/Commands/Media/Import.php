<?php

namespace App\Console\Commands\Media;

use App\Models\Channel;
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
    protected $signature = 'media:import {path} {channel=Administrator} {user=administrator} {collection=videos} {limit=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import media file(s) to channel';

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
        $channel = $this->firstOrCreateChannel();

        $i = 0;

        foreach ($this->getFilesInPath() as $file) {
            $this->info("Importing {$file->getFilename()}");

            $media = $channel
                ->addMedia($file->getRealPath())
                ->usingName($file->getFilename())
                ->toMediaCollection($this->argument('collection'));

            $media->setStatus('pending', 'needs processing');

            // Limit the number of imports
            if (++$i == $this->argument('limit')) {
                break;
            }
        }
    }

    /**
     * @return Channel
     */
    protected function firstOrCreateChannel(): Channel
    {
        $user = User::findBySlugOrFail($this->argument('user'));

        $model = $user->channels()->firstOrCreate(
            ['name' => $this->argument('channel')]
        );

        if (!$model->status()) {
            $model->setStatus('published');
        }

        return $model;
    }

    /**
     * @return Finder
     */
    protected function getFilesInPath(): Finder
    {
        // Ignore unreadable files
        $filter = function (\SplFileInfo $file) {
            if (!$file->isReadable() || !$file->isWritable()) {
                return false;
            }

            $mime = mime_content_type($file->getRealPath());

            return in_array($mime, $this->supportedMimeTypes());
        };

        return (new Finder())
            ->files()
            ->in($this->argument('path'))
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
        $extensions = collect(
            config('vod.extensions')
        );

        return $extensions->map(function ($item) {
            return "*.{$item}";
        })->toArray();
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return config('vod.mimetypes');
    }
}
