<?php

namespace App\Console\Commands\Video;

use App\Models\Collection;
use App\Services\VideoImportService;
use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import {path} {collection=Series}';

    /**
     * @var string
     */
    protected $description = 'Import media files to a collection';

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
    public function handle(VideoImportService $videoImportService)
    {
        $videoImportService->import(
            $this->getCollectionModel(),
            $this->argument('path'),
        );
    }

    /**
     * @return Collection
     */
    protected function getCollectionModel(): Collection
    {
        return Collection::firstWhere(
            'name', $this->argument('collection')
        );
    }
}
