<?php

namespace App\Console\Commands\Scout;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use Throwable;

class DeleteIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:delete-indexes';

    /**
     * @var string
     */
    protected $description = 'Delete scout indexes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $client = new Client(config('meilisearch.host'), config('meilisearch.key'));

        $indexes = collect(config('meilisearch.indexes'));

        try {
            $indexes->each(function ($item) use ($client): void {
                $client->deleteIndex($item['name']);

                $this->info("Index {$item['name']} has been deleted.");
            });
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
