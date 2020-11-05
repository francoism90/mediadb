<?php

namespace App\Console\Commands\Library;

use App\Services\LibraryService;
use Illuminate\Console\Command;

class Maintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform maintenance on library models';

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
    public function handle(LibraryService $libraryService): void
    {
        $libraryService->performMaintenance();
    }
}
