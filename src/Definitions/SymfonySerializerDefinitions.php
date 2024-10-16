<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Definitions for Symfony Serializer.
 *
 * Requires `symfony/property-access`.
 *
 * @see https://github.com/symfony/serializer
 *
 * @version 1.0.0
 */
final class SymfonySerializerDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[SerializerInterface::class] = function (
            ObjectNormalizer $objectNormalizer,
            JsonEncoder $jsonEncoder
        ) {
            return new Serializer([$objectNormalizer], [$jsonEncoder]);
        };

        return $c;
    }
}
