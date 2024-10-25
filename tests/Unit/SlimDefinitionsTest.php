<?php

declare(strict_types=1);

use Reun\PhpAppDefinitions\Definitions\SlimDefinitions;
use Reun\PhpAppDefinitions\Definitions\SlimPsrHttpDefinitions;
use Slim\App;
use Tests\TestCase;

describe(SlimDefinitions::class, function () {
    it("should be constructed successfully", function () {
        $container = TestCase::createContainer(array_merge(
            SlimDefinitions::getDefinitions(),
            SlimPsrHttpDefinitions::getDefinitions(),
        ));

        foreach ([
            App::class => App::class,
        ] as $entry => $result) {
            expect($container->get($entry))->toBeInstanceOf($entry);
            expect($container->get($entry))->toBeInstanceOf($result);
        }
    });
});
