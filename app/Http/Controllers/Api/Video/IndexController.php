<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\Video\SimilarFilter;
use App\Support\QueryBuilder\Filters\Video\TypeFilter;
use App\Support\QueryBuilder\Sorters\MostViewsSorter;
use App\Support\QueryBuilder\Sorters\RandomSorter;
use App\Support\QueryBuilder\Sorters\RelevanceSorter;
use App\Support\QueryBuilder\Sorters\TrendingSorter;
use App\Support\QueryBuilder\Sorters\Video\DurationSorter;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    public function __invoke(): ResourceCollection
    {
        $defaultSort = AllowedSort::custom('relevance', new RelevanceSorter())
            ->defaultDirection('desc');

        $videos = QueryBuilder::for(Video::class)
            ->allowedAppends([
                'clip',
                'favorite',
                'following',
                'poster_url',
                'views',
            ])
            ->allowedIncludes([
                'model',
                'tags',
            ])
            ->allowedFilters([
                AllowedFilter::scope('tags', 'withTags')->ignore(null, '*'),
                AllowedFilter::custom('similar', new SimilarFilter())->ignore(null, '*'),
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

        return new VideoCollection($videos);
    }
}
