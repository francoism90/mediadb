<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class SortByName
{
    public function execute(): void
    {
        Tag::setNewOrder(
            $this->getTags()
        );
    }

    private function getTags(): Collection
    {
        return Tag::all()->sortBy('name', SORT_NATURAL);
    }
}
