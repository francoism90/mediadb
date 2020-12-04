<?php

namespace App\Console\Commands\Video;

use App\Services\VideoService;
use Illuminate\Console\Command;

class Maintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform maintenance on video models';

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
    public function handle(VideoService $videoService): void
    {
        $videoService->performMaintenance();
    }
}
