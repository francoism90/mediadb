<?php

namespace App\Console\Commands\Scout;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use Throwable;

class CreateIndexesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'scout:create-indexes
        {--r|reset : Reset settings of an existing index}';

    /**
     * @var string
     */
    protected $description = 'Creates or recreates scout indexes';

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
                $index = $client->getOrCreateIndex($item['name'], [
                    'primaryKey' => 'id',
                ]);

                if ($this->option('reset')) {
                    $index->resetSettings();

                    $this->info("Index {$item['name']} has been reset.");
                }

                $index->updateSettings($item['settings']);

                $index->updateSynonyms(config('meilisearch.synonyms'));
                $index->updateStopWords(config('meilisearch.stop_words'));

                $this->info("Index {$item['name']} has been created.");
            });
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
