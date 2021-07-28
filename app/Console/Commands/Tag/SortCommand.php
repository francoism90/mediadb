<?php

namespace App\Console\Commands\Tag;

use App\Services\TagService;
use Illuminate\Console\Command;

class SortCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tag:sort';

    /**
     * @var string
     */
    protected $description = 'Sort tags by name';

    public function handle(): void
    {
        TagService::sortByName();
    }
}
