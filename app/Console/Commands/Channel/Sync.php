<?php

namespace App\Console\Commands\Channel;

use App\Services\ChannelSyncService;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach model tags to child models';

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
    public function handle(ChannelSyncService $channelSyncService)
    {
        $channelSyncService->sync();
    }
}
