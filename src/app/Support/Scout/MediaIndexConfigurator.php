<?php

namespace App\Support\Scout;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class MediaIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [];
}
