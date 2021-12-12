<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GetSimilarVideos
{
    /**
     * @var string
     */
    public const QUERY_FILTER = '/[\p{L}\p{N}\p{S}]+/u';

    public function __invoke(Video $video): Builder
    {
        $ids = $this->getIds($video);
        $idsOrder = implode(',', $ids);

        return Video::active()
            ->whereIn('id', $ids)
            ->orderByRaw(sprintf('FIELD(id, %s)', $idsOrder));
    }

    protected function getIds(Video $video): array
    {
        $items = $this->getWithPhrases($video);
        $items = $items->merge($this->withTagsOfAnyType($video));

        return $items
            ->where('id', '<>', $video->id)
            ->pluck('id')
            ->unique()
            ->take(75)
            ->toArray();
    }

    protected function getWithPhrases(Video $video): Collection
    {
        $query = Str::of($video->name)->matchAll(self::QUERY_FILTER)->take(8);

        $items = collect();

        // e.g. foo bar 1, foo bar, foo
        for ($i = $query->count(); $i >= 1; --$i) {
            $phrase = $query->take($i)->implode(' ');

            if (strlen($phrase) < 1) {
                continue;
            }

            $results = app(SearchForVideos::class)([
                'query' => $phrase,
                'limit' => 10,
            ])->get();

            $items = $items->merge($results);
        }

        return $items;
    }

    protected function withTagsOfAnyType(Video $video): Collection
    {
        return Video::cacheFor(60 * 10)
            ->select('id')
            ->with('tags')
            ->withAnyTagsOfAnyType($video->tags)
            ->inRandomOrder()
            ->take(75)
            ->get();
    }
}
