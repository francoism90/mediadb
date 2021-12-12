<?php

namespace App\Traits;

use App\Models\Tag;
use Spatie\Tags\HasTags;

trait InteractsWithTags
{
    use HasTags;

    public function extractTagTranslations(string $field = 'name', ?string $type = null): array
    {
        $items = $this
            ->tags
            ->when($type, fn ($query, $type) => $query->where('type', $type))
            ->map(fn (Tag $item) => $item->$field)
            ->unique();

        return $items->values()->all();
    }
}
