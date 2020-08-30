<?php

namespace App\Console\Commands\Video;

use App\Services\VideoSyncService;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync videos';

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
    public function handle(VideoSyncService $videoSyncService)
    {
        $videoSyncService->sync();
    }
}
