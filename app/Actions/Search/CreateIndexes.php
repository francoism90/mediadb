<?php

namespace App\Actions\Search;

use Illuminate\Support\Collection;
use MeiliSearch\Client;

class CreateIndexes
{
    public function __invoke(): void
    {
        $client = $this->getClient();

        $settings = config('meilisearch.settings', []);

        $this->getIndexes()->each(function ($item) use ($client, $settings): void {
            // Delete index (if exists)
            $client->deleteIndex($item['name']);

            // Create index
            $client->createIndex($item['name']);

            // Update settings
            $client->index($item['name'])->updateSettings(
                array_merge($settings, $item['settings'])
            );
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
