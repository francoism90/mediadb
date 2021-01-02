<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [
        __DIR__.'/app',
        // __DIR__.'/config',
        // __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

    // Define what rule sets will be applied
    $parameters->set(Option::SETS, [
        SetList::LARAVEL_60,
        SetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        SetList::LARAVEL_CODE_QUALITY,
        SetList::PHP_74,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::ORDER,
        SetList::PERFORMANCE,
        SetList::TYPE_DECLARATION,
    ]);

    // is your PHP version different from the one your refactor to? [default: your PHP version], uses PHP_VERSION_ID format
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);

    // auto import fully qualified class names? [default: false]
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
