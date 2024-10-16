<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Reun\PhpAppConfig\Config\DefinitionsInterface;

/**
 * Definitions for HTML Purifier.
 *
 * @see https://github.com/ezyang/htmlpurifier
 *
 * @version 1.0.0
 */
final class HtmlPurifierDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        // Allow overriding config separately
        $c[\HTMLPurifier_Config::class] = function (): \HTMLPurifier_Config {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set("HTML.Nofollow", true);
            $config->set("HTML.TargetBlank", true);

            return $config;
        };

        $c[\HTMLPurifier::class] = fn (\HTMLPurifier_Config $c) => new \HTMLPurifier($c);

        return $c;
    }
}
