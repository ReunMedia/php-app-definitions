<?php

declare(strict_types=1);

use JMS\Serializer\SerializerInterface;
use Reun\PhpAppDefinitions\Definitions\JmsSerializerDefinitions;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestAppConfig;
use Tests\TestCase;

test(JmsSerializerDefinitions::class, function () {
    $container = TestCase::createContainer(JmsSerializerDefinitions::getDefinitions());

    foreach ([
        SerializerInterface::class => SerializerInterface::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }
});

afterAll(function () {
    // Remove cache directory
    $appConfig = new TestAppConfig();
    $filesystem = new Filesystem();
    $filesystem->remove($appConfig->cacheDirectory);
});
