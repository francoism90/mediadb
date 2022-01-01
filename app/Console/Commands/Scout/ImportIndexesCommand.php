<?php

namespace App\Console\Commands\Scout;

use App\Actions\Search\ImportIndexes;
use Illuminate\Console\Command;

class ImportIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:import-indexes';

    /**
     * @var string
     */
    protected $description = 'Import Laravel Models';

    public function handle(
        ImportIndexes $importIndexes
    ): void {
        $importIndexes();
    }
}
