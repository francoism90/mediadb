<?php

namespace App\Console\Commands;

use App\Services\TagService;
use Illuminate\Console\Command;

class SortTags extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tag:sort';

    /**
     * @var string
     */
    protected $description = 'Sort tags by name';

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
    public function handle(TagService $tagService): void
    {
        $tagService->sortByName();
    }
}
