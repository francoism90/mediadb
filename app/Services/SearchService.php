<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchService
{
    public const FILTER_NUMBERS = '/[\p{N}]+/u';
    public const FILTER_PHRASES = '/[\p{L}\p{N}]+/u';
    public const FILTER_PUNCTUATIONS = '/[\p{P}]+/u';
    public const FILTER_WORDS = '/[\p{L}]+/u';

    public function excerptSearch(Model $model, string $value): Collection
    {
        $models = self::getPhraseResults($model, $value);
        $models = $models->merge(self::getWordResults($model, $value));
        $models = $models->merge(self::getNumberResults($model, $value));

        return $models->merge(self::getPunctuationsResults($model, $value));
    }

    public static function getPhraseResults(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::FILTER_PHRASES)->take(8);

        $models = collect();

        // e.g. foo bar 1, foo bar, foo
        for ($i = $value->count(); $i >= 1; --$i) {
            $phrase = $value->take($i)->implode(' ');

            $models = $models->merge(
                $model->search($phrase)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getNumberResults(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::FILTER_NUMBERS)->take(8);

        $models = collect();

        for ($i = $value->count(); $i >= 0; --$i) {
            $number = $value[$i] ?? '';

            if (strlen($number) < 1) {
                continue;
            }

            $models = $models->merge(
                $model->search($number)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getWordResults(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::FILTER_WORDS)->take(8);

        $models = collect();

        for ($i = $value->count(); $i >= 0; --$i) {
            $word = $value[$i] ?? '';

            if (strlen($word) < 1) {
                continue;
            }

            $models = $models->merge(
                $model->search($word)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getPunctuationsResults(Model $model, string $value, int $limit = 500): Collection
    {
        $value = Str::of($value)->matchAll(self::FILTER_PUNCTUATIONS)->take(4);

        $models = collect();

        for ($i = $value->count(); $i >= 0; --$i) {
            $punctuation = $value[$i] ?? '';

            if (strlen($punctuation) < 1) {
                continue;
            }

            $models = $models->merge(
                $model->search($punctuation)->take($limit)->get()
            );
        }

        return $models;
    }

    public static function getTagResults(Model $model, Collection | array $value, int $limit = 500): Collection
    {
        return $model
            ->with('tags')
            ->withAnyTagsOfAnyType($value)
            ->inRandomSeedOrder()
            ->take($limit)
            ->get();
    }
}
