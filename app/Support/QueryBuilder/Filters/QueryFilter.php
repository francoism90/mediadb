<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    public const TAG_REGEX = '/tag:([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;
        $value = $this->sanitize($value);

        $queryModels = $this->getQueryModels($query, $value);
        $tagModels = $this->getTagModels($value);

        $modelCount = $queryModels->count() + $tagModels->count();

        // Force empty result
        if (0 === $modelCount) {
            return $query->whereNull('id');
        }

        return $query
            ->when($queryModels->isNotEmpty(), function ($query) use ($queryModels) {
                $ids = $queryModels->pluck('id');
                $idsOrder = $queryModels->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            })
            ->when($tagModels->isNotEmpty(), fn ($query) => $query
                ->withAnyTagsOfAnyType($tagModels)
                ->inRandomSeedOrder()
            )
            ->take(10000)
            ->orderBy('id');
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getQueryModels(Builder $query, string $value = ''): Collection
    {
        // Replace tags (e.g. tag:foo)
        $value = Str::of($value)->replaceMatches(self::TAG_REGEX, '')->trim();

        // Skip query on empty value
        if ($value->isEmpty()) {
            return collect();
        }

        return $query
            ->getModel()
            ->simpleQueryString((string) $value, 10000)
            ->get();
    }

    /**
     * @param string $value
     *
     * @return Collection
     */
    protected function getTagModels(string $value = ''): Collection
    {
        // Match valid tags (e.g. tag:foo)
        $matches = Str::of($value)->matchAll(self::TAG_REGEX);

        // Skip queries on empty matches
        if ($matches->isEmpty()) {
            return collect();
        }

        return Tag::withSlugTranslated($matches->toArray())->get();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function sanitize(string $value = ''): ?string
    {
        $value = filter_var(
            $value,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW |
            FILTER_FLAG_STRIP_BACKTICK |
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        // Replace dots and underscores
        $value = str_replace(['.', ',', '_'], ' ', $value);

        // Replace whitespace with a single space
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}
