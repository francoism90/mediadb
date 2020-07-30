<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\Filters\Filter;

class WatchedFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Get viewed models
        $models = $this->getUserMediaViewed()->unique('subject_id');

        // Get modelIds
        $ids = $models->pluck('subject_id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @return Collection
     */
    protected function getUserMediaViewed(): Collection
    {
        return Activity::causedBy(auth()->user())
            ->where('description', 'viewed')
            ->where('subject_type', Media::class)
            ->orderByDesc('created_at')
            ->take(150)
            ->get();
    }
}
