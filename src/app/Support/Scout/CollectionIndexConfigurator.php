<?php

namespace App\Support\Scout;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class CollectionIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [];
}
