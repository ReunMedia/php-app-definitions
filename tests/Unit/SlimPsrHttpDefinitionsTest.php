<?php

declare(strict_types=1);

use DI\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Reun\PhpAppDefinitions\Definitions\SlimPsrHttpDefinitions;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

test(SlimPsrHttpDefinitions::class, function () {
    $container = new Container(SlimPsrHttpDefinitions::getDefinitions());

    foreach ([
        ResponseFactoryInterface::class => ResponseFactory::class,
        StreamFactoryInterface::class => StreamFactory::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }
});
