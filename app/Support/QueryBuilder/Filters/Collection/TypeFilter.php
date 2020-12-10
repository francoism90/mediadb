<?php

namespace App\Support\QueryBuilder\Filters\Collection;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    /**
     * @var array
     */
    protected $types = ['subscribed'];

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
    protected function getSubscribedModels()
    {
        return optional(auth()->user(), function ($user) {
            return $user->subscriptions(Collection::class)->get();
        });
    }
}
