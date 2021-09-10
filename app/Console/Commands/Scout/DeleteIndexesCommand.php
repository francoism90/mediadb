<?php

namespace App\Console\Commands\Scout;

use App\Actions\Search\DeleteIndexes;
use Illuminate\Console\Command;

class DeleteIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:delete-indexes';

    /**
     * @var string
     */
    protected $description = 'Delete Laravel Scout indexes';

    public function handle(): void
    {
        app(DeleteIndexes::class);
    }
}
