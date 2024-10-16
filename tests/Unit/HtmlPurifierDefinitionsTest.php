<?php

declare(strict_types=1);

use DI\Container;
use Reun\PhpAppDefinitions\Definitions\HtmlPurifierDefinitions;

test(HtmlPurifierDefinitions::class, function () {
    $container = new Container(HtmlPurifierDefinitions::getDefinitions());

    foreach ([
        HTMLPurifier_Config::class => HTMLPurifier_Config::class,
        HTMLPurifier::class => HTMLPurifier::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }
});
