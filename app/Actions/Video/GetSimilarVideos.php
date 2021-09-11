<?php

namespace App\Actions\Video;

use App\Actions\Search\QueryDocuments;
use App\Actions\Tag\GetWithTagsOfAnyType;
use App\Models\Video;
use Illuminate\Support\Collection;

class GetSimilarVideos
{
    public function execute(Video $video, int $limit = 100): Collection
    {
        $models = $this->queryDocuments($video, $limit);
        $models = $models->merge($this->withTagsOfAnyType($video, $limit));

        return $models->take($limit);
    }

    protected function queryDocuments(Video $video, int $limit): Collection
    {
        return app(QueryDocuments::class)->execute(
            $video,
            $video->name,
            $limit,
        );
    }

    protected function withTagsOfAnyType(Video $video, int $limit): Collection
    {
        return app(GetWithTagsOfAnyType::class)->execute(
            $video,
            $video->tags,
            $limit,
        );
    }
}
