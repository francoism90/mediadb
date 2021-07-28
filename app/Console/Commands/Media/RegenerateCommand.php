<?php

namespace App\Console\Commands\Media;

use App\Services\SyncService;
use Illuminate\Console\Command;

class RegenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:regenerate';

    /**
     * @var string
     */
    protected $description = 'Regenerate media models';

    public function handle(): void
    {
        SyncService::handleMissingMetadata();
        SyncService::handleMissingConversions();
    }
}
