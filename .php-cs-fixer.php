<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('_ide_helper.php')
    ->exclude('bootstrap')
    ->exclude('storage')
    ->exclude('vendor')
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
