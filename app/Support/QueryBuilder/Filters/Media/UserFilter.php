<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UserFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Find model
        $model = User::findBySlugOrFail($value);

        // Return Builder
        return $query->where('model_type', User::class)
                     ->where('model_id', $model->id);
    }
}
