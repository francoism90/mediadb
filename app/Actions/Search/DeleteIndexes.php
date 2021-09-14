<?php

namespace App\Actions\Search;

use Illuminate\Support\Collection;
use MeiliSearch\Client;

class DeleteIndexes
{
    public function __invoke(): void
    {
        $client = $this->getClient();

        $this->getIndexes()->each(function (array $item) use ($client): void {
            $index = $client->getOrCreateIndex($item['name'], [
                'primaryKey' => 'id',
            ]);

            $index->delete();
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