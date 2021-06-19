<?php

namespace App\Services;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageService
{
    public function optimize(string $path): string
    {
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize($path);

        return $path;
    }
}
