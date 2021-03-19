<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchService
{
    public const FILTER_NUMBER_REGEX = '/[\p{N}]+/u';
    public const FILTER_PUNCTUATION_REGEX = '/[\p{P}]+/u';
    public const FILTER_WORD_REGEX = '/[\p{L}]+/u';
    public const QUERY_INPUT_LIMIT = 8;
    public const QUERY_RESULT_LIMIT = 500;

    /**
     * @var Collection
     */
    protected $results;

    /**
     * @param Builder      $query
     * @param string|array $value
     *
     * @return void
     */
    public function search(Builder $query, $value): void
    {
        $value = $this->sanitizeQuery($value);

        // Reset current results
        $this->resetResults();

        // Perform partial searching (e.g. this book 1, this book, this)
        for ($i = $value->take(self::QUERY_INPUT_LIMIT)->count(); $i >= 1; --$i) {
            $queryValue = $value->take($i)->implode(' ');

            if (strlen($queryValue) <= 1) {
                continue;
            }

            $this->results = $this->results->merge(
                $query
                    ->getModel()
                    ->search($queryValue)
                    ->take(self::QUERY_RESULT_LIMIT)
                    ->get()
            );
        }
    }

    /**
     * @return Collection
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * @return void
     */
    public function resetResults(): void
    {
        $this->results = collect();
    }

    /**
     * @param string $value
     *
     * @return Collection
     */
    protected function sanitizeQuery(string $value = ''): Collection
    {
        // Improve matching by converting to ascii
        $query = Str::ascii($value);

        // Find all words
        $query = Str::of($value)->matchAll(self::FILTER_WORD_REGEX);

        // Merge with numbers
        $query = $query->merge(
            Str::of($value)->matchAll(self::FILTER_NUMBER_REGEX)
        );

        // Merge with punctuations
        $query = $query->merge(
            Str::of($value)->matchAll(self::FILTER_PUNCTUATION_REGEX)
        );

        return $query;
    }
}
