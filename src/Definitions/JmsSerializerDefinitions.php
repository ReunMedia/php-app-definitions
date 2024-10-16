<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;

/**
 * Definitions for JMS Serializer.
 *
 * @see https://github.com/schmittjoh/serializer
 *
 * @version 1.0.0
 */
final class JmsSerializerDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[SerializerInterface::class] = function (AbstractAppConfig $appConfig) {
            $cacheDir = "{$appConfig->cacheDirectory}/JmsSerializer";
            is_dir($cacheDir) || mkdir($cacheDir, 0755, true);

            return SerializerBuilder::create()
                ->setCacheDir($cacheDir)
                ->setDebug($appConfig->isDev())
                ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
                ->build()
            ;
        };

        return $c;
    }
}
