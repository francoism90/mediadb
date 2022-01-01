<?php

namespace App\Actions\Search;

use Illuminate\Support\Facades\Artisan;

class SyncIndexes
{
    public function __invoke(): void
    {
        $models = config('api.scout.sync_models');

        foreach ($models as $model) {
            $this->importModels($model);
        }
    }

    protected function importModels(string $model): void
    {
        Artisan::call('scout:import', [
            'model' => $model,
        ]);
    }
}
