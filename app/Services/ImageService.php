<?php

namespace App\Services;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageService
{
    /**
     * @param string $path
     *
     * @return self
     */
    public function optimize(string $path): self
    {
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize($path);

        return $this;
    }
}
