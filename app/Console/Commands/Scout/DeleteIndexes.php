<?php

namespace App\Console\Commands\Scout;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use Throwable;

class DeleteIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:delete-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete scout indexes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $client = new Client(config('meilisearch.host'), config('meilisearch.key'));

        $indexes = collect(config('meilisearch.indexes'));

        try {
            $indexes->each(function ($item) use ($client): void {
                $client->deleteIndex($item['name']);

                $this->info('Index "'.$item['name'].'" deleted.');
            });
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
