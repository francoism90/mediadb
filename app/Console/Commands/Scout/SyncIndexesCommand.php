<?php

namespace App\Console\Commands\Scout;

use App\Actions\Search\SyncIndexes;
use Illuminate\Console\Command;

class SyncIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:sync';

    /**
     * @var string
     */
    protected $description = 'Sync Laravel Scout indexes';

    public function handle(
        SyncIndexes $syncIndexes
    ): void {
        $syncIndexes();
    }
}
