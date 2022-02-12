<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class SyncTagsWithTypes
{
    public function __invoke(Model $model, array $values = null): void
    {
        $model->detachTags($model->tags);

        $tags = collect($values)->map(function ($value) {
            if ($value instanceof Tag) {
                return $value;
            }

            return Tag::findByHashid($value);
        });

        $names = fn ($type) => $tags->where('type', $type)->pluck('name')->unique()->all();

        $tags
            ?->pluck('type')
            ?->unique()
            ?->each(fn ($type) => $model->syncTagsWithType($names($type), $type));
    }
}
