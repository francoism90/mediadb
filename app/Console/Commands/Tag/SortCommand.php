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

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(TagService $tagService): void
    {
        $tagService->sortByName();
    }
}
