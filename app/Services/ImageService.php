<?php

namespace App\Services;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageService
{
    /**
     * @param string $path
     *
     * @return void
     */
    public function optimize(string $path): void
    {
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize($path);
    }
}
