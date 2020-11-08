<?php

namespace App\Support\QueryBuilder\Filters\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    /**
     * @var array
     */
    protected $types = ['favorited', 'liked'];

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $models = collect();

        foreach ($this->types as $type) {
            if (!in_array($type, $value)) {
                continue;
            }

            $methodName = Str::camel("get-{$type}-models");

            $models = $models->merge(
                $this->$methodName()
            );
        }

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @return Collection
     */
    protected function getFavoritedModels(): Collection
    {
        return auth()->user()->favorites(Video::class)->get();
    }

    /**
     * @return Collection
     */
    protected function getLikedModels(): Collection
    {
        return auth()->user()->likes(Video::class)->get();
    }
}
