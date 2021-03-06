<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\RelatedFilter;
use App\Support\QueryBuilder\Filters\Video\TypeFilter;
use App\Support\QueryBuilder\Sorters\MostViewsSorter;
use App\Support\QueryBuilder\Sorters\RandomSorter;
use App\Support\QueryBuilder\Sorters\RecommendedSorter;
use App\Support\QueryBuilder\Sorters\TrendingSorter;
use App\Support\QueryBuilder\Sorters\Video\DurationSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    public function __invoke()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $videos = QueryBuilder::for(Video::class)
            ->allowedAppends([
                'clip',
            ])
            ->allowedIncludes([
                'model',
                'tags',
            ])
            ->allowedFilters([
                AllowedFilter::scope('tags', 'tags.with_slug')->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::field('name')->defaultDirection('asc'),
                AllowedSort::field('created_at')->defaultDirection('asc'),
                AllowedSort::field('updated_at')->defaultDirection('asc'),
                AllowedSort::custom('duration', new DurationSorter())->defaultDirection('asc'),
                AllowedSort::custom('random', new RandomSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return VideoResource::collection($videos);
    }
}
