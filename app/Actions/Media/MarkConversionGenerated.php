<?php

namespace App\Actions\Media;

use App\Models\Media;
use Spatie\QueueableAction\QueueableAction;

class MarkConversionGenerated
{
    use QueueableAction;

    public function execute(Media $media, string $name): void
    {
        $media->markAsConversionGenerated($name);
    }
}
