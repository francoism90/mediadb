<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CollectionService
{
    public const COLLECTION_STATUS = 'private';

    /**
     * @param Model      $model
     * @param Collection $collections
     *
     * @return Collection
     */
    public function create(Model $model, Collection $collections): Collection
    {
        $models = collect();

        foreach ($collections as $collection) {
            $name = $collection['name'] ?? $model->name;
            $status = $collection['status'] ?? self::COLLECTION_STATUS;

            $collectionModel = $model->collections()->firstOrCreate(
                ['name' => $name]
            );

            if (!$collectionModel->status()) {
                $collectionModel->setStatus($status);
            }

            $models->push($collectionModel);
        }

        return $models;
    }
}
