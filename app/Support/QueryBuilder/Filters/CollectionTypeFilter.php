<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class CollectionTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        switch ($value) {
            case 'user':
                return $query->where('user_id', Auth::user()->id ?? 0);
                break;
            default:
                return $query;
        }
    }
}
