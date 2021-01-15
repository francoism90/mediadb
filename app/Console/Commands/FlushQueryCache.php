<?php

namespace App\Console\Commands;

use App\Models\Tag;
use App\Models\Video;
use Illuminate\Console\Command;

class FlushQueryCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query-cache:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush query caches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Tag::flushQueryCache();
        Video::flushQueryCache();
    }
}
