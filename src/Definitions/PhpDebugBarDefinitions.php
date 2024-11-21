<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use Doctrine\ORM\EntityManagerInterface;
use Middlewares\Debugbar as DebugBarMiddleware;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Reun\PhpAppConfig\Config\DefinitionsInterface;

/**
 * Definitions for PHP DebugBar.
 *
 * Includes DebugBarMiddleware that injects DebugBar to PSR-7 response.
 * Automatically configures collectors for Doctrine and Monolog if they exist in
 * the container.
 *
 * @see https://github.com/maximebf/php-debugbar
 * @see https://github.com/middlewares/debugbar
 *
 * @version 1.0.0
 */
final class PhpDebugBarDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[DebugBar::class] = function (ContainerInterface $c): DebugBar {
            $debugBar = new StandardDebugBar();

            // Add conditional collectors
            // Must use FQN here instead of `static` for container compiling
            PhpDebugBarDefinitions::addPdoCollector($debugBar, $c);
            PhpDebugBarDefinitions::addMonologCollector($debugBar, $c);

            return $debugBar;
        };

        $c[DebugBarMiddleware::class] = function (
            DebugBar $debugBar,
            ResponseFactoryInterface $responseFactory,
            StreamFactoryInterface $streamFactory,
        ): DebugBarMiddleware {
            $mw = new DebugBarMiddleware($debugBar, $responseFactory, $streamFactory);
            $mw->inline();
            $mw->captureAjax();

            return $mw;
        };

        return $c;
    }

    private static function addPdoCollector(
        DebugBar $debugBar,
        ContainerInterface $c
    ): void {
        // Only add PDO collector when using Doctrine
        if (!$c->has(EntityManagerInterface::class)) {
            return;
        }

        $em = $c->get(EntityManagerInterface::class);
        $conn = $em->getConnection()->getNativeConnection();

        if ($conn instanceof \PDO) {
            $debugBar->addCollector(new PDOCollector(new TraceablePDO($conn)));
        }
    }

    private static function addMonologCollector(
        DebugBar $debugBar,
        ContainerInterface $c
    ): void {
        // This is a minor hack to make sure we only use Monolog if it's
        // actually defined in the container. If we simply check for existence
        // of `Logger`, it's always `true` because of autowiring of the concrete
        // class.
        //
        // This is why we first check for `LoggerInterface` and then also ensure
        // it's actually a Monolog Logger.

        if (!$c->has(LoggerInterface::class)) {
            return;
        }

        $logger = $c->get(LoggerInterface::class);
        if ($logger instanceof Logger) {
            $debugBar->addCollector(new MonologCollector($logger));
        }
    }
}
