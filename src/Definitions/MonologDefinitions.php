<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;

/**
 * Definitions for Monolog.
 *
 * @see https://github.com/Seldaek/monolog
 *
 * @version 1.0.0
 */
final class MonologDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[Logger::class] = function (AbstractAppConfig $config): Logger {
            $handlers = [
                new StreamHandler("{$config->dataDirectory}/logs/webapp.log", Level::Info),
                new StreamHandler("php://stdout"),
            ];

            return new Logger("default", $handlers);
        };

        $c[LoggerInterface::class] = fn (Logger $logger): Logger => $logger;

        return $c;
    }
}
