<?php

namespace App\Support\MediaLibrary;

use App\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class PathGenerator extends DefaultPathGenerator
{
    /*
     * Get the path for assets of the given media, relative to the root storage path.
     */
    public function getPathForAssets(Media $media): string
    {
        return $this->getBasePath($media).'/assets/';
    }
}
