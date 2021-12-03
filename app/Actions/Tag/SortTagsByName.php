<?php

namespace App\Actions\Tag;

use App\Models\Tag;

class SortTagsByName
{
    public function __invoke(): void
    {
        Tag::setNewOrder(
            $this->getTagsSorted()
        );
    }

    protected function getTagsSorted(): array
    {
        return Tag::active()
            ->orderBy('name')
            ->pluck('id')
            ->all();
    }
}
