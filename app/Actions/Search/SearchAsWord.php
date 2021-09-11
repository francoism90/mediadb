<?php

namespace App\Actions\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchAsWord
{
    public function __invoke(Model $model, array | string $value, int $limit = 500): Collection
    {
        $value = is_string($value) ? explode(' ', $value) : $value;

        $value = collect($value)->shuffle()->take(6);

        $models = collect();

        foreach ($value as $word) {
            if (strlen($word) < 2) {
                continue;
            }

            $models = $models->merge(
                $model->search($word)->take($limit)->get()
            );
        }

        return $models->take($limit);
    }
}
