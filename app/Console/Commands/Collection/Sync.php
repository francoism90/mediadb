<?php

namespace App\Console\Commands\Collection;

use App\Services\CollectionSyncService;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync collections';

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
    public function handle(CollectionSyncService $collectionSyncService)
    {
        $collectionSyncService->sync();
    }
}
