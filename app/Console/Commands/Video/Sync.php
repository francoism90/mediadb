<?php

namespace App\Console\Commands\Video;

use App\Services\Video\SyncService;
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
     * @param SyncService $syncService
     *
     * @return void
     */
    public function handle(SyncService $syncService)
    {
        $syncService->sync();
    }
}
