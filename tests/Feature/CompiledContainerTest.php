<?php

declare(strict_types=1);

use Composer\ClassMapGenerator\ClassMapGenerator;
use DI\ContainerBuilder;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestAppConfig;

// @phpstan-ignore method.notFound
test('container can be compiled with all definitions', function () {
    $c = [];

    $classMap = ClassMapGenerator::createMap(__DIR__.'/../../src/Definitions');
    foreach ($classMap as $symbol => $path) {
        if (is_a($symbol, DefinitionsInterface::class, true)) {
            fwrite(STDOUT, "Adding {$symbol}".PHP_EOL);
            $c += $symbol::getDefinitions();
        }
    }

    // Create container builder
    $containerBuilder = new ContainerBuilder();

    // Add Definitions
    $appConfig = new TestAppConfig();
    $containerBuilder->addDefinitions($c);
    $containerBuilder->enableCompilation("{$appConfig->cacheDirectory}/php-di");
    $containerBuilder->build();
})->throwsNoExceptions();

afterAll(function () {
    // Remove Doctrine directory
    $appConfig = new TestAppConfig();
    $filesystem = new Filesystem();
    $diCacheDir = "{$appConfig->cacheDirectory}/php-di/";
    $filesystem->remove($diCacheDir);
});
