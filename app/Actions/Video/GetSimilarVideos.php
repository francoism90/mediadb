<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GetSimilarVideos
{
    /**
     * @var string
     */
    public const QUERY_FILTER = '/[\p{L}\p{N}\p{S}]+/u';

    public function __invoke(Video $video): Collection
    {
        $items = $this->getAsPhrases($video);
        $items = $items->merge($this->withTagsOfAnyType($video));

        return $items->where('id', '<>', $video->id);
    }

    protected function getAsPhrases(Video $video): Collection
    {
        $query = $this->getQueryTerms($video->name);

        $collect = collect();

        // e.g. foo bar 1, foo bar, foo
        for ($i = $query->count(); $i >= 1; --$i) {
            $phrase = $query->take($i)->implode(' ');

            if (strlen($phrase) < 1) {
                continue;
            }

            $items = Video::search($phrase)->take(50)->get();

            $collect = $collect->merge($items);
        }

        return $collect;
    }

    protected function withTagsOfAnyType(Video $video): Collection
    {
        return Video::has('tags')
            ->withAnyTagsOfAnyType($video->tags)
            ->inRandomOrder()
            ->take(50)
            ->get();
    }

    protected function getQueryTerms(string $value): Collection
    {
        return Str::of($value)->matchAll(self::QUERY_FILTER)->take(8);
    }
}
