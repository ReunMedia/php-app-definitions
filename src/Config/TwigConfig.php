<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Config;

use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Twig\Loader\FilesystemLoader;

/**
 * Configuration for Twig definitions.
 *
 * @version 1.0.0
 */
class TwigConfig
{
    /**
     * Twig FilesystemLoader paths.
     *
     * @var array<string,string>
     */
    public array $loaderPaths = [];

    public function __construct(AbstractAppConfig $appConfig)
    {
        $basePath = "{$appConfig->projectRoot}/src/view";
        $this->loaderPaths = [
            // Add base path to allow loading templates via file paths.
            $basePath => FilesystemLoader::MAIN_NAMESPACE,
            // Add pages path with @pages namespace that will be used by
            // DynamicTwigPage action.
            "{$basePath}/public/pages" => "pages",
        ];
    }
}
