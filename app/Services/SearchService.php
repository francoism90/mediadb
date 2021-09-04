<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchService
{
    public const QUERY_FILTER = '/[\p{L}\p{N}\p{S}]+/u';

    public function excerptSearch(Model $model, string $value): Collection
    {
        $models = self::getPhraseResults($model, $value);

        return $models->merge(self::getRandomResults($model, $value));
    }

    public static function getPhraseResults(Model $model, string $value, int $limit = 100): Collection
    {
        $value = Str::of($value)->matchAll(self::QUERY_FILTER)->take(6);

        $models = collect();

        // e.g. foo bar 1, foo bar, foo
        for ($i = $value->count(); $i >= 1; --$i) {
            $phrase = $value->take($i)->implode(' ');

            if (strlen($phrase) < 2) {
                continue;
            }

            $models = $models->merge(
                $model->search($phrase)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getRandomResults(Model $model, string $value, int $limit = 100): Collection
    {
        $value = Str::of($value)->matchAll(self::QUERY_FILTER)->take(6)->shuffle();

        $models = collect();

        foreach ($value as $word) {
            if (strlen($word) < 2) {
                continue;
            }

            $models = $models->merge(
                $model->search($word)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getTagResults(Model $model, Collection | array $value, int $limit = 100): Collection
    {
        return $model
            ->with('tags')
            ->withAnyTagsOfAnyType($value)
            ->inRandomSeedOrder()
            ->take($limit)
            ->get();
    }
}
