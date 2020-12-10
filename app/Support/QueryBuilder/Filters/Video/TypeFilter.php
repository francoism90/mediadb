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
        $types = is_string($value) ? explode(',', $value) : $value;

        // Merge models
        $models = collect();

        foreach ($types as $type) {
            if (!in_array($type, $this->types)) {
                continue;
            }

            $methodName = Str::camel("get-{$type}-models");

            $models = $models->merge(
                $this->$methodName() ?? []
            );
        }

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @return Collection|null
     */
    protected function getFavoritedModels()
    {
        return optional(auth()->user(), function ($user) {
            return $user->favorites(Video::class)->get();
        });
    }

    /**
     * @return Collection|null
     */
    protected function getLikedModels()
    {
        return optional(auth()->user(), function ($user) {
            return $user->likes(Video::class)->get();
        });
    }
}
