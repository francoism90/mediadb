<?php

namespace App\Console\Commands\Video;

use App\Models\Video;
use App\Services\Video\ImportService;
use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import {path} {video}';

    /**
     * @var string
     */
    protected $description = 'Import media to video model';

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
    public function handle(ImportService $importService)
    {
        $video = $this->getVideoModel();
        $collection = $this->getCollections();
        $locale = $this->getLocale();

        // Start importing
        $importService->import(
            $video,
            $this->argument('path'),
            $collection,
            [
                'locale' => $locale,
            ]
        );
    }

    /**
     * @return string
     */
    protected function getCollections(): string
    {
        return $this->choice(
            'Choose collection',
            config('vod.import.collections'),
        );
    }

    /**
     * @return string
     */
    protected function getLocale(): string
    {
        return $this->choice(
            'Choose locale',
            config('vod.import.locales'),
            locale()
        );
    }

    /**
     * @return Video
     */
    protected function getVideoModel(): Video
    {
        return Video::findOrFail(
            $this->argument('video')
        );
    }
}
