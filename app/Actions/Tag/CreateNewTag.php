<?php

namespace App\Actions\Tag;

use App\Models\Tag;

class CreateNewTag
{
    public function __invoke(array $data = []): Tag
    {
        $value = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return Tag::findOrCreate(
            $value('name'),
            $value('type', 'genre'),
            $value('locale', 'en'),
        );
    }
}
