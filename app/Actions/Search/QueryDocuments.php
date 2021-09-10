<?php

namespace App\Actions\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QueryDocuments
{
    public const QUERY_FILTER = '/[\p{L}\p{N}\p{S}]+/u';

    public function execute(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::QUERY_FILTER)->implode(' ');

        $models = app(SearchAsPhase::class)->execute($model, $value);
        $models = $models->merge(app(SearchAsWord::class)->execute($model, $value));

        return $models->take($limit);
    }
}
