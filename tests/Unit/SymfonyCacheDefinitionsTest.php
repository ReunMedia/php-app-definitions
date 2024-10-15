<?php

declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppDefinitions\Definitions\SymfonyCacheDefinitions;
use Symfony\Component\Cache\Psr16Cache;
use Tests\TestAppConfig;
use Tests\TestCase;

describe(SymfonyCacheDefinitions::class, function () {
    it("should create writable cache directory and cache DB file if they don't exist", function () {
        /** @var TestCase $this */
        $container = $this->createContainer(SymfonyCacheDefinitions::getDefinitions());

        /** @var AbstractAppConfig $appConfig */
        $appConfig = $container->get(AbstractAppConfig::class);

        /** @var CacheInterface $cache */
        $cache = $container->get(CacheInterface::class);

        $cacheDir = "{$appConfig->cacheDirectory}";
        expect($cacheDir)->toBeWritableDirectory();
    });

    it("should be constructed successfully", function () {
        /** @var TestCase $this */
        $container = $this->createContainer(SymfonyCacheDefinitions::getDefinitions());

        foreach ([
            CacheInterface::class => Psr16Cache::class,
        ] as $entry => $result) {
            expect($container->get($entry))->toBeInstanceOf($entry);
            expect($container->get($entry))->toBeInstanceOf($result);
        }
    });

    it("should write to cache database", function () {
        /** @var TestCase $this */
        $container = $this->createContainer(SymfonyCacheDefinitions::getDefinitions());

        /** @var AbstractAppConfig $appConfig */
        $appConfig = $container->get(AbstractAppConfig::class);

        /** @var CacheInterface $cache */
        $cache = $container->get(CacheInterface::class);

        $cache->set("test", "test");

        $cacheDir = "{$appConfig->cacheDirectory}";
        expect("{$cacheDir}/cacheDb.sqlite")->toBeWritableFile();
    });
});

afterAll(function () {
    // Remove cache DB file and directory
    $appConfig = new TestAppConfig();
    $cacheDir = "{$appConfig->cacheDirectory}";
    assert(unlink("{$cacheDir}/cacheDb.sqlite"), "Cache DB file couldn't be removed");
    assert(rmdir($appConfig->cacheDirectory), "Cache directory couldn't be removed");
});
