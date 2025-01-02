<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Middlewares\ContentType;
use Psr\Container\ContainerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Reun\PhpAppDefinitions\Config\SlimConfig;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;

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
 * @uses Reun\PhpAppDefinitions\Config\SlimConfig to configure definitions.
 *
 * @version 2.0.0
 */
final class SlimDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[App::class] = function (
            AbstractAppConfig $config,
            ContainerInterface $c,
            SlimConfig $slimConfig,
        ): App {
            $app = AppFactory::createFromContainer($c);
            $app->addRoutingMiddleware();

            // Add middleware

            // Content type, content length and output buffering middlewares are
            // hardcoded at certain positions in the middleware stack to ensure
            // compatibility.
            $middleware = $slimConfig->getMiddleware();

            // Content type middleware must be placed after Debugbar because of
            // its reliance on response Content-Type header.
            array_unshift($middleware, ContentType::class);

            $middleware[] = OutputBufferingMiddleware::class;

            // Content length middleware should be placed on the end of the
            // middleware stack so that it gets executed first and exited last.
            // This avoids content length mismatch.
            $middleware[] = ContentLengthMiddleware::class;

            foreach ($middleware as $mw) {
                $app->add($mw);
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
