<?php

namespace App\Support\QueryBuilder\Filters\Collection;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MediaFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Media model
        $media = Media::findByHash($value);

        return $query->whereJsonContains(
            'custom_properties->media',
            ['media_id' => $media->id]
        );
    }
}
