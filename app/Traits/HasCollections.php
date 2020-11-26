<?php

namespace App\Traits;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use InvalidArgumentException;

trait HasCollections
{
    /**
     * @return string
     */
    public static function getCollectionClassName(): string
    {
        return Collection::class;
    }

    /**
     * @return MorphToMany
     */
    public function collections(): MorphToMany
    {
        return $this
            ->morphToMany(Collection::class, 'collectable');
    }

    /**
     * @param string $locale
     */
    public function collectionsTranslated($locale = null): MorphToMany
    {
        $locale = !is_null($locale) ? $locale : app()->getLocale();

        return $this
            ->morphToMany(self::getCollectionClassName(), 'collectable')
            ->select('*')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) as name_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) as slug_translated");
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $collections
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllCollections(Builder $query, $collections, string $type = null): Builder
    {
        $collections = static::convertToCollections($collections, $type);

        collect($collections)->each(function ($collection) use ($query) {
            $query->whereHas('collections', function (Builder $query) use ($collection) {
                $query->where('collections.id', $collection ? $collection->id : 0);
            });
        });

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $collections
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyCollections(Builder $query, $collections, string $type = null): Builder
    {
        $collections = static::convertToCollections($collections, $type);

        return $query->whereHas('collections', function (Builder $query) use ($collections) {
            $collectionIds = collect($collections)->pluck('id');

            $query->whereIn('collections.id', $collectionIds);
        });
    }

    /**
     * @param Builder $query
     * @param mixed   $collections
     *
     * @return Builder
     */
    public function scopeWithAllCollectionsOfAnyType(Builder $query, $collections): Builder
    {
        $collections = static::convertToCollectionsOfAnyType($collections);

        collect($collections)->each(function ($collection) use ($query) {
            $query->whereHas('collections', function (Builder $query) use ($collection) {
                $query->where('collections.id', $collection ? $collection->id : 0);
            });
        });

        return $query;
    }

    /**
     * @param Builder $query
     * @param mixed   $collections
     *
     * @return Builder
     */
    public function scopeWithAnyCollectionsOfAnyType(Builder $query, $collections): Builder
    {
        $collections = static::convertToCollectionsOfAnyType($collections);

        return $query->whereHas('collections', function (Builder $query) use ($collections) {
            $collectionIds = collect($collections)->pluck('id');

            $query->whereIn('collections.id', $collectionIds);
        });
    }

    /**
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function collectionsWithType(string $type = null)
    {
        return $this->collections->filter(function (Collection $collection) use ($type) {
            return $collection->type === $type;
        });
    }

    /**
     * @param array|\ArrayAccess|Collection $collections
     * @param string|null                   $type
     *
     * @return $this
     */
    public function attachCollections($collections, string $type = null)
    {
        $className = static::getCollectionClassName();

        $collections = collect($className::findOrCreate($collections, $type));

        $this->collections()->syncWithoutDetaching($collections->pluck('id')->toArray());

        return $this;
    }

    /**
     * @param string|Collection $collection
     * @param string|null       $type
     *
     * @return $this
     */
    public function attachCollection($collection, string $type = null)
    {
        return $this->attachCollections([$collection], $type);
    }

    /**
     * @param array|\ArrayAccess $collections
     * @param string|null        $type
     *
     * @return $this
     */
    public function detachCollections($collections, string $type = null)
    {
        $collections = static::convertToCollections($collections, $type);

        collect($collections)
            ->filter()
            ->each(function (Collection $collection) {
                $this->collections()->detach($collection);
            });

        return $this;
    }

    /**
     * @param string|Collection $collection
     * @param string|null       $type
     *
     * @return $this
     */
    public function detachCollection($collection, string $type = null)
    {
        return $this->detachCollections([$collection], $type);
    }

    /**
     * @param array|\ArrayAccess $collections
     *
     * @return $this
     */
    public function syncCollections($collections)
    {
        $className = static::getCollectionClassName();

        $collections = collect($className::findOrCreate($collections));

        $this->collections()->sync($collections->pluck('id')->toArray());

        return $this;
    }

    /**
     * @param array|\ArrayAccess $collections
     * @param string|null        $type
     *
     * @return $this
     */
    public function syncCollectionsWithType($collections, string $type = null)
    {
        $className = static::getCollectionClassName();

        $collections = collect($className::findOrCreate($collections, $type));

        $this->syncCollectionIds($collections->pluck('id')->toArray(), $type);

        return $this;
    }

    /**
     * @param mixed $values
     * @param mixed $type
     * @param mixed $locale
     *
     * @return mixed
     */
    protected static function convertToCollections($values, $type = null, $locale = null)
    {
        return collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof Collection) {
                if (isset($type) && $value->type != $type) {
                    throw new InvalidArgumentException("Type was set to {$type} but collection is of type {$value->type}");
                }

                return $value;
            }

            $className = static::getCollectionClassName();

            return $className::findFromString($value, $type, $locale);
        });
    }

    /**
     * @param mixed $values
     * @param mixed $locale
     *
     * @return mixed
     */
    protected static function convertToCollectionsOfAnyType($values, $locale = null)
    {
        return collect($values)->map(function ($value) use ($locale) {
            if ($value instanceof Collection) {
                return $value;
            }

            $className = static::getCollectionClassName();

            return $className::findFromStringOfAnyType($value, $locale);
        });
    }

    /**
     * Use in place of eloquent's sync() method so that the collection type may be optionally specified.
     *
     * @param array       $ids
     * @param string|null $type
     * @param bool        $detaching
     */
    protected function syncCollectionIds($ids, string $type = null, $detaching = true)
    {
        $isUpdated = false;

        // Get a list of collection_ids for all current collections
        $current = $this->collections()
            ->newPivotStatement()
            ->where('collectable_id', $this->getKey())
            ->where('collectable_type', $this->getMorphClass())
            ->when(null !== $type, function ($query) use ($type) {
                $collectionModel = $this->collections()->getRelated();

                return $query->join(
                    $collectionModel->getTable(),
                    'collectables.collection_id',
                    '=',
                    $collectionModel->getTable().'.'.$collectionModel->getKeyName()
                )
                    ->where('collections.type', $type);
            })
            ->pluck('collection_id')
            ->all();

        // Compare to the list of ids given to find the collections to remove
        $detach = array_diff($current, $ids);

        if ($detaching && count($detach) > 0) {
            $this->collections()->detach($detach);

            $isUpdated = true;
        }

        // Attach any new ids
        $attach = array_unique(array_diff($ids, $current));

        if (count($attach) > 0) {
            collect($attach)->each(function ($id) {
                $this->collections()->attach($id, []);
            });

            $isUpdated = true;
        }

        // Once we have finished attaching or detaching the records, we will see if we
        // have done any attaching or detaching, and if we have we will touch these
        // relationships if they are configured to touch on any database updates.
        if ($isUpdated) {
            $this->collections()->touchIfTouching();
        }
    }
}
