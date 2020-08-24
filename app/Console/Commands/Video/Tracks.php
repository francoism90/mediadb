<?php

namespace App\Console\Commands\Video;

use App\Models\Video;
use App\Services\VideoTrackImportService;
use Illuminate\Console\Command;

class Tracks extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import-tracks {path} {model} {type=subtitles}';

    /**
     * @var string
     */
    protected $description = 'Import track files to a video';

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
    public function handle(VideoTrackImportService $videoTrackImportService)
    {
        $videoTrackImportService->import(
            $this->getVideoModel(),
            $this->argument('path'),
            $this->argument('type'),
        );
    }

    /**
     * @return Video
     */
    protected function getVideoModel(): Video
    {
        return Video::findByHash(
            $this->argument('model')
        );
    }
}
