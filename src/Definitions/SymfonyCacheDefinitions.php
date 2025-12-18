<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Psr\SimpleCache\CacheInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Sets `symfony/cache` as default PSR-16 implementation.
 *
 * @see https://github.com/symfony/cache
 *
 * @version 1.0.0
 */
final class SymfonyCacheDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[CacheInterface::class] = function (AbstractAppConfig $appConfig): CacheInterface {
            // Create cache directory if it doesn't exist.
            $cacheDir = "{$appConfig->cacheDirectory}";
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }

            $pdoAdapter = new PdoAdapter("sqlite:{$cacheDir}/cacheDb.sqlite");

            // TODO - This is not completely reliable
            // - https://github.com/symfony/symfony/issues/32569
            // - https://github.com/symfony/symfony/issues/33166
            try {
                $pdoAdapter->createTable();
            } catch (\PDOException $e) {
                if (!str_contains($e->getMessage(), 'table cache_items already exists')) {
                    throw $e;
                }
            }

            return new Psr16Cache($pdoAdapter);
        };

        return $c;
    }
}
