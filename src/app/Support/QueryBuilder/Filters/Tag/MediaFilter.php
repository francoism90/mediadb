<?php

namespace App\Support\QueryBuilder\Filters\Tag;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class MediaFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $tagIds = $this->getTaggableModels()->pluck('tag_id')->toArray();

        return $query->whereIn('id', $tagIds);
    }

    /**
     * @return Collection
     */
    private function getTaggableModels(): Collection
    {
        return DB::table('taggables')
            ->distinct()
            ->select('tag_id')
            ->where('taggable_type', Media::class)
            ->get();
    }
}
