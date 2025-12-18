<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Reun\PhpAppDefinitions\Config\SlimConfig;
use Reun\PhpAppDefinitions\Definitions\SlimDefinitions;
use Reun\PhpAppDefinitions\Definitions\SlimPsrHttpDefinitions;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\TestCase;

class TestMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $res = $handler->handle($request);
        $res->getBody()->write('TestMiddleware');

        return $res;
    }
}

class CustomSlimConfig extends SlimConfig
{
    public function getMiddlewareClasses(): array
    {
        return [TestMiddleware::class];
    }
}

class CustomSlimConfig2 extends SlimConfig
{
    public function getMiddlewareClasses(): array
    {
        $mw = parent::getMiddlewareClasses();
        $mw[] = TestMiddleware::class;

        return $mw;
    }
}

test('custom Slim configuration class with middleware override', function () {
    $c = [];
    $c[SlimConfig::class] = fn (CustomSlimConfig $x): SlimConfig => $x;
    $c += SlimDefinitions::getDefinitions();
    $c += SlimPsrHttpDefinitions::getDefinitions();
    $container = TestCase::createContainer($c);

    $slim = $container->get(App::class);
    $slim->get('/', fn ($req, $res, $args) => $res);

    $response = $slim->handle((new ServerRequestFactory())->createServerRequest(
        'GET',
        '/'
    ));
    expect((string) $response->getBody())->toBe('TestMiddleware');
});

test('custom Slim configuration class with middleware appending', function () {
    $c = [];
    $c[SlimConfig::class] = fn (CustomSlimConfig2 $x): SlimConfig => $x;
    $c += SlimDefinitions::getDefinitions();
    $c += SlimPsrHttpDefinitions::getDefinitions();
    $container = TestCase::createContainer($c);

    $slim = $container->get(App::class);
    $slim->get('/', fn ($req, $res, $args) => $res);

    $response = $slim->handle((new ServerRequestFactory())->createServerRequest(
        'GET',
        '/'
    ));
    $body = (string) $response->getBody();
    expect($body)
        ->not()->toBe('TestMiddleware')
        ->and($body)->toContain('TestMiddleware')
    ;

    // Restore error handlers (probably set by Whoops)
    restore_error_handler();
    restore_exception_handler();
});
