<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('_ide_helper.php')
    ->exclude('bootstrap')
    ->exclude('storage')
    ->exclude('vendor')
;

$config = new Config();

return $config->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
