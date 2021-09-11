<?php

namespace App\Actions\Search;

use Illuminate\Support\Collection;
use MeiliSearch\Client;

class CreateIndexes
{
    public function __invoke(?bool $reset = false): void
    {
        $client = $this->getClient();

        $this->getIndexes()->each(function ($item) use ($client, $reset): void {
            $index = $client->getOrCreateIndex($item['name'], [
                'primaryKey' => 'id',
            ]);

            if ($reset) {
                $index->resetSettings();
            }

            $index->updateSettings($item['settings']);
            $index->updateSynonyms(config('meilisearch.synonyms'));
            $index->updateStopWords(config('meilisearch.stop_words'));
        });
    }

    protected function getIndexes(): Collection
    {
        return collect(config('meilisearch.indexes'));
    }

    protected function getClient(): Client
    {
        return app(Client::class, [
            config('meilisearch.host'),
            config('meilisearch.key'),
        ]);
    }
}
