<?php

namespace App\Console\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Command;

class CreateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tag:create {name} {type=genre}';

    /**
     * @var string
     */
    protected $description = 'Create a tag model';

    public function handle(): void
    {
        $model = Tag::create([
            'name' => $this->argument('name'),
            'type' => $this->argument('type'),
        ]);

        $this->info(sprintf('Successfully created tag %s (%s)', $model->name, $model->id));
    }
}
