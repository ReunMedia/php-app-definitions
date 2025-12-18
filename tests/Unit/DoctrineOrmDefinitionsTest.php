<?php

declare(strict_types=1);

use Doctrine\DBAL\Logging\Middleware;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppDefinitions\Definitions\DoctrineOrmDefinitions;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestAppConfig;
use Tests\TestCase;

describe(DoctrineOrmDefinitions::class, function () {
    it('should create writable directory for Doctrine DB file', function () {
        $container = TestCase::createContainer(DoctrineOrmDefinitions::getDefinitions());

        $appConfig = $container->get(AbstractAppConfig::class);
        $container->get(EntityManagerInterface::class);
        $doctrineDir = "{$appConfig->sharedDataDirectory}/doctrine/";
        expect($doctrineDir)->toBeWritableDirectory();
    });

    it('should be constructed successfully', function () {
        $container = TestCase::createContainer(DoctrineOrmDefinitions::getDefinitions());

        foreach ([
            EntityManagerInterface::class => EntityManager::class,
            ObjectManager::class => EntityManagerInterface::class,
        ] as $entry => $result) {
            expect($container->get($entry))->toBeInstanceOf($entry);
            expect($container->get($entry))->toBeInstanceOf($result);
        }
    });

    it('should add logger in dev mode if one exists', function () {
        /** @var TestCase $this */
        $c = array_merge(
            DoctrineOrmDefinitions::getDefinitions(),
            // @phpstan-ignore method.protected
            $this->stubDefinitions([LoggerInterface::class])
        );
        $container = TestCase::createContainer($c);

        // Test existence of Doctrine logger middleware
        $em = $container->get(EntityManagerInterface::class);
        $mw = $em->getConnection()->getConfiguration()->getMiddlewares();

        // @phpstan-ignore method.notFound
        expect($mw)->toContainInstanceOf(Middleware::class);
    });
});

afterAll(function () {
    // Remove Doctrine directory
    $appConfig = new TestAppConfig();
    $filesystem = new Filesystem();
    $doctrineDir = "{$appConfig->sharedDataDirectory}/doctrine/";
    $filesystem->remove($doctrineDir);
});
