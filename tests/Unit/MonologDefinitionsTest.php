<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppDefinitions\Definitions\MonologDefinitions;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestAppConfig;
use Tests\TestCase;

test(MonologDefinitions::class, function () {
    $container = TestCase::createContainer(MonologDefinitions::getDefinitions());

    foreach ([
        LoggerInterface::class => Logger::class,
    ] as $entry => $result) {
        expect($container->get($entry))->toBeInstanceOf($entry);
        expect($container->get($entry))->toBeInstanceOf($result);
    }

    $appConfig = $container->get(AbstractAppConfig::class);

    $logger = $container->get(LoggerInterface::class);
    $logger->info("Test Message");

    expect("{$appConfig->dataDirectory}/logs/webapp.log")->toBeFile();
});

afterAll(function () {
    // Remove logs directory
    $appConfig = new TestAppConfig();
    $filesystem = new Filesystem();
    $filesystem->remove("{$appConfig->dataDirectory}/logs");
});
