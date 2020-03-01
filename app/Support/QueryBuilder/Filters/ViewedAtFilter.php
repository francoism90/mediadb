<?php

namespace App\Support\QueryBuilder\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class ViewedAtFilter implements Filter
{
    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        $model = get_class($query->getModel());

        return $query->whereHas('views', function (Builder $q) use ($model, $value) {
            $q->where('viewable_type', $model)
              ->where('visitor', Auth::user()->id)
              ->where('collection', 'user-history')
              ->whereDate('viewed_at', Carbon::parse($value));
        });
    }
}
