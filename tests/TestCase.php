<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Container\ContainerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;

abstract class TestCase extends BaseTestCase
{
    public ContainerInterface $container;

    /**
     * @param array<class-string,mixed> $definitions
     */
    public static function createContainer(array $definitions = []): Container
    {
        $definitions = array_merge(
            $definitions,
            [
                AbstractAppConfig::class => fn (TestAppConfig $x) => $x,
            ]
        );

        return new Container($definitions);
    }
}
