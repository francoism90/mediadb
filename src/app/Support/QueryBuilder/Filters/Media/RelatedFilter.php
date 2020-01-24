<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $collections = $this->getMediaCollections(
            Media::getModelByKey((string) $value)
        );

        $models = collect();

        foreach ($collections as $collection) {
            $models = $models->merge($collection->get());
        }

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @param Media $media
     *
     * @return array
     */
    private function getMediaCollections(Media $media): array
    {
        return [
            $media->relatedName(),
            $media->relatedTags(),
            $media->relatedUserModel(),
            $media->relatedRandom(),
        ];
    }
}
