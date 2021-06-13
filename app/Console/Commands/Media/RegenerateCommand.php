<?php

namespace App\Console\Commands\Media;

use App\Services\MediaSyncService;
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

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(
        MediaSyncService $syncService
    ): void {
        $syncService->handleMissingMetadata();
        $syncService->handleMissingConversions();
    }
}
