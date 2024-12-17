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

    /**
     * Creates stub definitions for DI container.
     *
     * @param class-string[] $classes
     *
     * @return array<class-string,\Closure>
     */
    protected function stubDefinitions(array $classes): array
    {
        $definitions = [];
        foreach ($classes as $class) {
            $definitions[$class] = fn () => static::createStub($class);
        }

        return $definitions;
    }
}
