<?php

declare(strict_types=1);

use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppDefinitions\Config\TwigConfig;
use Reun\PhpAppDefinitions\Definitions\TwigDefinitions;
use Tests\TestCase;
use Twig\Environment;
use Twig\Extra\Markdown\ErusevMarkdown;
use Twig\Extra\Markdown\MarkdownInterface;
use Twig\Loader\FilesystemLoader;

/**
 * @return array<class-string,mixed>
 */
function getTestDefinitions(): array
{
    $c = TwigDefinitions::getDefinitions();

    // Use custom config class that points Twig loader to test fixtures.
    $c[TwigConfig::class] = function (AbstractAppConfig $appConfig) {
        $twigConfig = new TwigConfig($appConfig);
        $twigConfig->loaderPaths = [
            "{$appConfig->projectRoot}/tests/Fixtures/Twig" => FilesystemLoader::MAIN_NAMESPACE,
        ];

        return $twigConfig;
    };

    return $c;
}

describe(TwigDefinitions::class, function () {
    it('should be constructed successfully', function () {
        $container = TestCase::createContainer(getTestDefinitions());

        foreach ([
            Environment::class => Environment::class,
            MarkdownInterface::class => ErusevMarkdown::class,
        ] as $entry => $result) {
            expect($container->get($entry))->toBeInstanceOf($entry);
            expect($container->get($entry))->toBeInstanceOf($result);
        }
    });

    it('should render markdown to HTML', function () {
        $container = TestCase::createContainer(getTestDefinitions());

        $twig = $container->get(Environment::class);
        expect($twig->render('test.twig'))->toBe("<h1>Hello World</h1>\n");
    });
});
