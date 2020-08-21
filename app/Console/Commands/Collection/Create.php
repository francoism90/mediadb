<?php

namespace App\Console\Commands\Collection;

use App\Models\User;
use App\Services\CollectionService;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'collection:create {name} {user=administrator}';

    /**
     * @var string
     */
    protected $description = 'Create a new collection for a user';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle(CollectionService $collectionService)
    {
        $user = User::findBySlugOrFail($this->argument('user'));

        $name = trim($this->argument('name'));

        $collectionService->create(
            $user,
            collect([
                ['name' => $name],
            ])
        );
    }
}
