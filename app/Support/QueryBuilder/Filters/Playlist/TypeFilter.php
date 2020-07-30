<?php

namespace App\Support\QueryBuilder\Filters\Playlist;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        return $query
            ->when('user' === $value, function ($query) {
                return $query->where('model_type', User::class)
                             ->where('model_id', auth()->user()->id);
            }, function ($query) {
                return $query->where('model_type', User::class)
                             ->whereNotIn('model_id', [auth()->user()->id]);
            });
    }
}
