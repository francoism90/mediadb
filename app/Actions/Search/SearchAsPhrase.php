<?php

namespace App\Actions\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchAsPhrase
{
    public function __invoke(Model $model, string $value, int $limit = 500): Collection
    {
        $value = collect($value)->take(6);

        $models = collect();

        // e.g. foo bar 1, foo bar, foo
        for ($i = $value->count(); $i >= 1; --$i) {
            $phrase = $value->take($i)->implode(' ');

            if (strlen($phrase) < 2) {
                continue;
            }

            $models = $models->merge(
                $model->search($phrase)->take($limit)->get()
            );
        }

        return $models->take($limit);
    }
}
