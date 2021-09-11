<?php

namespace App\Actions\Media;

use App\Models\Media;

class MarkConversionGenerated
{
    public function __invoke(Media $media, string $name): void
    {
        $media->markAsConversionGenerated($name);
    }
}
