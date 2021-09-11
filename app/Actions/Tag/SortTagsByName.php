<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class SortTagsByName
{
    public function __invoke(): void
    {
        Tag::setNewOrder(
            $this->getSortedTags()->toArray()
        );
    }

    protected function getSortedTags(): Collection
    {
        return Tag::all()->sortBy('name', SORT_NATURAL);
    }
}
