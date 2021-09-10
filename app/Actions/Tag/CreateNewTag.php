<?php

namespace App\Actions\Tag;

use App\Models\Tag;

class CreateNewTag
{
    public function execute(array $data = []): Tag
    {
        $collect = collect($data);

        return Tag::findOrCreate(
            $collect->get('name'),
            $collect->get('type'),
            $collect->get('locale'),
        );
    }
}
