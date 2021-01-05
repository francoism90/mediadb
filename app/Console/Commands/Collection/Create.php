<?php

namespace App\Console\Commands\Collection;

use App\Models\Collection;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'collection:create {name} {type=title} {overview?} {status=private}';

    /**
     * @var string
     */
    protected $description = 'Create a collection model';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $model = Collection::create([
            'name' => $this->argument('name'),
            'type' => $this->argument('type'),
            'overview' => $this->argument('overview'),
        ]);

        $model->setStatus($this->argument('status'));

        $this->info("Successfully created collection {$model->name} ({$model->id})");
    }
}
