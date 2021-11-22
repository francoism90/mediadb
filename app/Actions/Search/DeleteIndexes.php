<?php

namespace App\Actions\Search;

use Illuminate\Support\Collection;
use MeiliSearch\Client;

class DeleteIndexes
{
    public function __invoke(): void
    {
        $client = $this->getClient();

        $this->getIndexes()->each(
            fn (array $item) => $client->deleteIndexIfExists($item['name'])
        );
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
