<?php

namespace App\Actions\Video;

use App\Actions\Search\QueryDocuments;
use App\Actions\Tag\GetWithTagsOfAnyType;
use App\Models\Video;
use Illuminate\Support\Collection;

class GetSimilarVideos
{
    public function __invoke(Video $video, int $limit = 100): Collection
    {
        $models = $this->queryDocuments($video, $limit);
        $models = $models->merge($this->withTagsOfAnyType($video, $limit));

        return $models->where('id', '<>', $video->id)->take($limit);
    }

    protected function queryDocuments(Video $video, int $limit): Collection
    {
        return app(QueryDocuments::class)(
            $video,
            $video->name,
            $limit,
        );
    }

    protected function withTagsOfAnyType(Video $video, int $limit): Collection
    {
        return app(GetWithTagsOfAnyType::class)(
            $video,
            $video->tags,
            $limit,
        );
    }
}
