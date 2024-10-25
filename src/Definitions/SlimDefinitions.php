<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use DebugBar\DebugBar;
use Middlewares\ContentType;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Reun\PhpAppDefinitions\Utils;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

/**
 * Definitions for Slim Framework.
 *
 * Requires `middlewares/negotiation` and optionally supports
 * `middlewares/debugbar` and `zeuxisoo/slim-whoops`.
 *
 * @see https://github.com/slimphp/Slim
 * @see https://www.slimframework.com/
 * @see https://github.com/middlewares/negotiation
 * @see https://github.com/middlewares/debugbar
 * @see https://github.com/zeuxisoo/php-slim-whoops
 *
 * @version 1.0.0
 */
final class SlimDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[App::class] = function (
            AbstractAppConfig $config,
            ContainerInterface $c
        ): App {
            // Prepare middleware
            $mw = [];

            // Content type middleware must be placed after Debugbar because of
            // its reliance on response Content-Type header.
            $mw[] = ContentType::class;

            // Development only middleware
            if ($config->isDev()) {
                $mw[] = DebugBar::class;
                $mw[] = WhoopsMiddleware::class;
            }

            $mw[] = OutputBufferingMiddleware::class;

            // Content length middleware should be placed on the end of the
            // middleware stack so that it gets executed first and exited last.
            // This avoids content length mismatch.
            $mw[] = ContentLengthMiddleware::class;

            $app = AppFactory::createFromContainer($c);
            $app->addRoutingMiddleware();

            foreach ($mw as $cls) {
                $mwInstance = Utils::safeContainerGet($c, $cls);
                if ($mwInstance instanceof MiddlewareInterface) {
                    $app->add($mwInstance);
                }
            }

            // Setup ErrorRenderer in production
            if (!$config->isDev() && $c->has(ErrorRendererInterface::class)) {
                $errorRenderer = $c->get(ErrorRendererInterface::class);

                $errorHandler = $app
                    ->addErrorMiddleware($config->isDev(), true, true)
                    ->getDefaultErrorHandler()
                ;

                if ($errorHandler instanceof ErrorHandler) {
                    $errorHandler->registerErrorRenderer("text/html", $errorRenderer);
                }
            }

            $app->addBodyParsingMiddleware();

            return $app;
        };

        return $c;
    }
}
