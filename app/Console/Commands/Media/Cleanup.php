<?php

namespace App\Console\Commands\Media;

use App\Services\CleanupService;
use Illuminate\Console\Command;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup media models';

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
    public function handle(CleanupService $cleanupService)
    {
        $cleanupService->cleanup();
    }
}
