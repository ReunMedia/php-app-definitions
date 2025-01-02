<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Config;

use Middlewares\Debugbar;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppDefinitions\Utils;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

/**
 * Configuration for Slim definitions.
 *
 * @version 1.0.0
 */
class SlimConfig
{
    public function __construct(
        private ContainerInterface $container,
        protected AbstractAppConfig $appConfig
    ) {}

    /**
     * Return middleware that are added to Slim application.
     *
     * Override to provide custom Closure middleware. Use
     * {@see Reun\PhpAppDefinitions\Config\SlimConfig::getMiddlewareClasses()}
     * to provide class-based middleware.
     *
     * @return MiddlewareInterface[]
     */
    public function getMiddleware(): array
    {
        $mwInstances = [];

        foreach ($this->getMiddlewareClasses() as $cls) {
            $mwInstance = Utils::safeContainerGet($this->container, $cls);
            if ($mwInstance instanceof MiddlewareInterface) {
                $mwInstances[] = $mwInstance;
            }
        }

        return $mwInstances;
    }

    /**
     * Return list of middleware classes that are added to Slim application.
     *
     * Override to provide custom middleware. Content type, content length and
     * output buffering middlewares are hardcoded at certain positions in the
     * middleware stack to ensure compatibility. See
     * {@see Reun\PhpAppDefinitions\Definitions\SlimDefinitions} for more.
     *
     * @return list<class-string<MiddlewareInterface>>
     */
    public function getMiddlewareClasses(): array
    {
        // Prepare middleware
        $mw = [];

        // Development only middleware
        if ($this->appConfig->isDev()) {
            $mw[] = Debugbar::class;
            $mw[] = WhoopsMiddleware::class;
        }

        return $mw;
    }
}
