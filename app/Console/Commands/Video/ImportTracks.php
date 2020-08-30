<?php

namespace App\Console\Commands\Video;

use App\Models\Video;
use App\Services\VideoTrackImportService;
use Illuminate\Console\Command;

class ImportTracks extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import-tracks {video} {path}';

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
        $type = $this->choice('What is the track type?', ['subtitles']);
        $language = $this->choice('What is the track language?', ['en']);

        $videoTrackImportService->import(
            $this->getVideoModel(),
            $this->argument('path'),
            [
                'type' => $type,
                'language' => $language,
            ],
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
