<?php

namespace App\Console\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tag:create {name} {type=genre}';

    /**
     * @var string
     */
    protected $description = 'Create a tag model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $model = Tag::create([
            'name' => $this->argument('name'),
            'type' => $this->argument('type'),
        ]);

        $this->info("Successfully created tag {$model->name} ({$model->id})");
    }
}
