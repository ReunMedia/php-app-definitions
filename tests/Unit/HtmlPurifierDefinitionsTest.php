<?php

declare(strict_types=1);

use Reun\PhpAppDefinitions\Definitions\HtmlPurifierDefinitions;
use Tests\TestCase;

test(HtmlPurifierDefinitions::class, function () {
    $container = TestCase::createContainer(HtmlPurifierDefinitions::getDefinitions());

    foreach ([
        HTMLPurifier_Config::class => HTMLPurifier_Config::class,
        HTMLPurifier::class => HTMLPurifier::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }
});
