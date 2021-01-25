<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = PhpCsFixer\Finder::create()
    ->exclude('_ide_helper.php')
    ->exclude('bootstrap')
    ->exclude('storage')
    ->exclude('vendor')
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
