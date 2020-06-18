<?php

namespace App\Support\QueryBuilder\Filters\Playlist;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UserFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->where('model_type', User::class)
            ->where('model_id', auth()->user()->id);
    }
}
