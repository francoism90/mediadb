<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class SortTagsByName
{
    public function execute(): void
    {
        Tag::setNewOrder(
            $this->getSortedTags()
        );
    }

    protected function getSortedTags(): Collection
    {
        return Tag::all()->sortBy('name', SORT_NATURAL);
    }
}
