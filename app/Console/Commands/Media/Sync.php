<?php

namespace App\Console\Commands\Media;

use App\Services\MediaSyncService;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync media models';

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
    public function handle(MediaSyncService $mediaSyncService)
    {
        $mediaSyncService->sync(
            $this->option('force')
        );
    }
}
