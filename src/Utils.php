<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions;

use Psr\Container\ContainerInterface;

final class Utils
{
    /**
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return null|T
     */
    public static function safeContainerGet(ContainerInterface $container, string $id): ?object
    {
        $entry = $container->has($id)
            ? $container->get($id)
            : null;

        if (is_object($entry) && is_a($entry, $id)) {
            return $entry;
        }

        return null;
    }
}
