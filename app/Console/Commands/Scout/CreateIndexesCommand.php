<?php

namespace App\Console\Commands\Scout;

use App\Actions\Search\CreateIndexes;
use Illuminate\Console\Command;

class CreateIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:create-indexes';

    /**
     * @var string
     */
    protected $description = 'Create Laravel Scout indexes';

    public function handle(CreateIndexes $createIndexes): void
    {
        $createIndexes();
    }
}
