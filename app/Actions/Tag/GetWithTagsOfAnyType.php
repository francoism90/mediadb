<?php

namespace App\Actions\Tag;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GetWithTagsOfAnyType
{
    public function __invoke(
        Model $model,
        Collection | array $value,
        int $limit = 100
    ): Collection {
        return $model
            ->with('tags')
            ->withAnyTagsOfAnyType($value)
            ->inRandomSeedOrder()
            ->take($limit)
            ->get();
    }
}
