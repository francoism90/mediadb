<?php

namespace App\Traits;

use App\Models\User;

trait Relateable
{
    /**
     * @param QueryBuilder $query
     * @param int          $limit
     *
     * @return Collection
     */
    public function scopeRelatedName($query, int $limit = 9)
    {
        // Only keep alphanumeric
        $str = trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', $this->name));

        return $query->getModel()
            ->search("{$str}*")
            ->select(['name', 'description'])
            ->where('id', '<>', $this->id)
            ->collapse('id')
            ->from(0)
            ->take($limit);
    }

    /**
     * @param QueryBuilder $query
     *
     * @return Builder
     */
    public function scopeRelatedTags($query, int $limit = 6)
    {
        return $query
            ->select('id')
            ->whereKeyNot($this->id)
            ->withAnyTagsOfAnyType(
                $this->tags()->pluck('name')->toArray()
            )
            ->inRandomOrder(self::getRandomSeed())
            ->take($limit);
    }

    /**
     * @param QueryBuilder $query
     * @param User         $user
     *
     * @return Builder
     */
    public function scopeRelatedUserModel($query, int $limit = 6)
    {
        return $query
            ->select('id')
            ->whereKeyNot($this->id)
            ->where('model_type', User::class)
            ->where('model_id', $this->model_id)
            ->inRandomOrder(self::getRandomSeed())
            ->take($limit);
    }

    /**
     * @param QueryBuilder $query
     *
     * @return Builder
     */
    public function scopeRelatedRandom($query, int $limit = 6)
    {
        return $query
            ->select('id')
            ->whereKeyNot($this->id)
            ->inRandomOrder(self::getRandomSeed())
            ->take($limit);
    }
}
