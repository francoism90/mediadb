<?php

namespace App\Console\Commands\Collection;

use App\Models\Collection;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'collection:create {name} {type=title} {overview?}';

    /**
     * @var string
     */
    protected $description = 'Create a collection model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $model = Collection::create([
            'name' => $this->argument('name'),
            'type' => $this->argument('type'),
            'overview' => $this->argument('overview'),
        ]);

        $this->info("Successfully created collection {$model->name} ({$model->id})");
    }
}
