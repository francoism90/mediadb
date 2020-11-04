<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Support\QueryBuilder\Filters\FavoritedFilter;
use App\Support\QueryBuilder\Filters\LikedFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\RelatedFilter;
use App\Support\QueryBuilder\Filters\Video\CollectionFilter;
use App\Support\QueryBuilder\Sorts\FieldSorter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use App\Support\QueryBuilder\Sorts\Video\DurationSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    /**
     * @return VideoResource
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
                AllowedFilter::custom('favorited', new FavoritedFilter())->ignore(null, '*'),
                AllowedFilter::custom('liked', new LikedFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
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
