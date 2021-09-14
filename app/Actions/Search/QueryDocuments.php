<?php

namespace App\Actions\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QueryDocuments
{
    /**
     * @var string
     */
    public const QUERY_FILTER = '/[\p{L}\p{N}\p{S}]+/u';

    public function __invoke(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::QUERY_FILTER)->toArray();

        $models = app(SearchAsPhrase::class)($model, $value);
        $models = $models->merge(app(SearchAsWord::class)($model, $value));

        return $models->take($limit);
    }
}
