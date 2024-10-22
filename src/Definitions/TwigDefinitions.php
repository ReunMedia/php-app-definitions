<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use App\Twig\AppExtension;
use Psr\Container\ContainerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Reun\PhpAppDefinitions\Config\TwigConfig;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\Markdown\ErusevMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownInterface;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\ContainerRuntimeLoader;

/**
 * Definitions for Twig.
 *
 * @see https://github.com/twigphp/Twig
 *
 * @version 1.0.0
 */
final class TwigDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        // Automatic Markdown extension
        if (interface_exists(MarkdownInterface::class)) {
            if (class_exists(\Parsedown::class)) {
                // Parsedown is injected manually instead of relying on
                // autowiring to allow use of custom Parsedown registered in the
                // container.
                $c[MarkdownInterface::class] = fn (\Parsedown $x) => new ErusevMarkdown($x);
            }
        }

        //
        // Twig Environment configuration.
        //
        $c[Environment::class] = function (
            AbstractAppConfig $appConfig,
            TwigConfig $twigConfig,
            ContainerInterface $c,
        ): Environment {
            #region Extensions
            $extensions = [];

            // Automatic optional extensions
            /** @disregard P1009 because we're using `class_exists` */
            foreach ([
                // @phpstan-ignore class.notFound (because we're using `class_exists`)
                AppExtension::class,
                MarkdownExtension::class,
                IntlExtension::class,
            ] as $ext) {
                if (class_exists($ext)) {
                    $extensions[] = $c->get($ext);
                }
            }

            // Add debug extension in dev environment
            if ($appConfig->isDev()) {
                $extensions[] = $c->get(DebugExtension::class);
            }

            // Twig settings
            $settings = [
                "debug" => $appConfig->isDev(),
            ];
            #endregion

            #region Environment setup
            $loader = new FilesystemLoader();

            // Add loader paths from config
            foreach ($twigConfig->loaderPaths as $path => $namespace) {
                $loader->addPath($path, $namespace);
            }

            // Create Twig environment
            $twig = new Environment($loader, $settings);
            $twig->addRuntimeLoader(new ContainerRuntimeLoader($c));

            // Add extensions to Twig
            foreach ($extensions as $ext) {
                if ($ext instanceof ExtensionInterface) {
                    $twig->addExtension($ext);
                }
            }

            // Set Timezone
            $twig->getExtension(CoreExtension::class)->setTimezone(
                $appConfig->defaultTimezone
            );
            #endregion

            return $twig;
        };

        return $c;
    }
}
