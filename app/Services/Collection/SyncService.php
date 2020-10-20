<?php

namespace App\Services\Collection;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as IlluminateCollection;

class SyncService
{
    /**
     * @param Model $model
     * @param array $collections
     *
     * @return Collection
     */
    public function create(Model $model, array $collections): IlluminateCollection
    {
        $collect = collect();

        $locale = app()->getLocale();

        foreach ($collections as $collection) {
            $attributes = collect($collection);

            $collectionModel = $model->collections()->updateOrCreate(
                ["name->{$locale}" => $attributes->get('name')],
                $attributes->only(['name', 'type', 'overview'])->all()
            );

            $collect->push($collectionModel);
        }

        return $collect;
    }
}
