<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class MediaTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        switch ($value) {
            case 'user':
                return $query->where('model_type', User::class)
                             ->where('model_id', Auth::user()->id);
                break;
            default:
                return $query;
        }
    }
}
