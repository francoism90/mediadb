<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class CollectionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $models = $this->getMediaCollection(
            Media::getModelByKey((string) $value)
        );

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @param Media $media
     *
     * @return Collection
     */
    private function getMediaCollection(Media $media): Collection
    {
        return $media->relatedRandom(9)->get();
    }
}
