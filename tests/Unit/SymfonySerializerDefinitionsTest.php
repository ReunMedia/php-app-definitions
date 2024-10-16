<?php

declare(strict_types=1);

use Reun\PhpAppDefinitions\Definitions\SymfonySerializerDefinitions;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tests\TestCase;

test(SymfonySerializerDefinitions::class, function () {
    $container = TestCase::createContainer(SymfonySerializerDefinitions::getDefinitions());

    foreach ([
        SerializerInterface::class => Serializer::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }
});
