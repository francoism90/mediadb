<?php

namespace App\Console\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Command;

class SortByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:sort';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sort tags alphabetically';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sortTags();

        $this->info('Tags have been sorted!');
    }

    private function sortTags(): void
    {
        $tags = Tag::ordered()->get();

        $tagsArray = [];
        foreach ($tags as $tag) {
            $tagsArray[$tag->id] = $tag->name;
        }

        asort($tagsArray);

        Tag::setNewOrder(array_keys($tagsArray));
    }
}
