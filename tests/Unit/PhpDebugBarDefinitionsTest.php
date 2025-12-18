<?php

declare(strict_types=1);

use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DebugBar;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Middlewares\Debugbar as DebugBarMiddleware;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Reun\PhpAppDefinitions\Definitions\PhpDebugBarDefinitions;
use Tests\TestCase;

describe(PhpDebugBarDefinitions::class, function () {
    it("should be constructed successfully", function () {
        /** @var TestCase $this */
        $c = array_merge(
            PhpDebugBarDefinitions::getDefinitions(),
            // @phpstan-ignore method.protected
            $this->stubDefinitions([
                ResponseFactoryInterface::class,
                StreamFactoryInterface::class,
            ])
        );
        $container = TestCase::createContainer($c);

        foreach ([
            DebugBar::class => DebugBar::class,
            DebugBarMiddleware::class => DebugBarMiddleware::class,
        ] as $entry => $result) {
            expect($container->get($entry))->toBeInstanceOf($entry);
            expect($container->get($entry))->toBeInstanceOf($result);
        }
    });

    it("should add PDOCollector for Doctrine EntityManager if present", function () {
        // "Don't mock what you don't own"
        // Use in-memory database instead.
        $em = new EntityManager(DriverManager::getConnection([
            "driver" => "pdo_sqlite",
            "memory" => true,
        ]), ORMSetup::createAttributeMetadataConfiguration([], true));

        /** @var TestCase $this */
        $c = array_merge(
            PhpDebugBarDefinitions::getDefinitions(),
            [EntityManagerInterface::class => $em]
        );

        $container = TestCase::createContainer($c);

        $debugBar = $container->get(DebugBar::class);
        // @phpstan-ignore-next-line
        expect($debugBar->getCollectors())->toContainInstanceOf(PDOCollector::class);
    });

    it("should add MonologCollector for Monolog if present", function () {
        $logger = new Logger("default");

        /** @var TestCase $this */
        $c = array_merge(
            PhpDebugBarDefinitions::getDefinitions(),
            [LoggerInterface::class => $logger]
        );

        $container = TestCase::createContainer($c);

        $debugBar = $container->get(DebugBar::class);
        // @phpstan-ignore-next-line
        expect($debugBar->getCollectors())->toContainInstanceOf(MonologCollector::class);
    });
});
