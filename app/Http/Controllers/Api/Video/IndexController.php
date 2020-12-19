<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Support\QueryBuilder\Filters\RelatedFilter;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Filters\Video\CollectionFilter;
use App\Support\QueryBuilder\Filters\Video\TypeFilter;
use App\Support\QueryBuilder\Sorters\FieldSorter;
use App\Support\QueryBuilder\Sorters\MostViewsSorter;
use App\Support\QueryBuilder\Sorters\RecommendedSorter;
use App\Support\QueryBuilder\Sorters\TrendingSorter;
use App\Support\QueryBuilder\Sorters\Video\DurationSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $videos = QueryBuilder::for(Video::class)
            ->allowedAppends([
                'duration',
                'thumbnail_url',
            ])
            ->allowedIncludes([
                'model',
                'collections',
                'tags',
            ])
            ->allowedFilters([
                AllowedFilter::custom('collection', new CollectionFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new FieldSorter())->defaultDirection('asc'),
                AllowedSort::custom('created_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('duration', new DurationSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return VideoResource::collection($videos);
    }
}
