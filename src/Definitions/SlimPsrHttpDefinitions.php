<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Default PSR HTTP definitions provided by `generator-reun-webapp`.
 *
 * @author Kimmo Salmela <kimmo.salmela@reun.eu>
 * @copyright 2020 Reun Media
 *
 * @see https://gitlab.com/reun/webdev/generator-reun-webapp
 *
 * @version 1.0.0
 */
final class SlimPsrHttpDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[ResponseFactoryInterface::class] = fn (ResponseFactory $x): ResponseFactoryInterface => $x;

        $c[StreamFactoryInterface::class] = fn (StreamFactory $x): StreamFactoryInterface => $x;

        return $c;
    }
}
