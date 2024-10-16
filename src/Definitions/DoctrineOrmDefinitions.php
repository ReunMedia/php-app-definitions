<?php

declare(strict_types=1);

namespace Reun\PhpAppDefinitions\Definitions;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Reun\PhpAppConfig\Config\AbstractAppConfig;
use Reun\PhpAppConfig\Config\DefinitionsInterface;
use Reun\PhpAppDefinitions\Utils;

/**
 * Definitions for Doctrine ORM.
 *
 * @see https://github.com/doctrine/orm
 *
 * @version 1.0.0
 */
final class DoctrineOrmDefinitions implements DefinitionsInterface
{
    public static function getDefinitions(): array
    {
        $c = [];

        $c[EntityManagerInterface::class] = function (
            AbstractAppConfig $appConfig,
            ContainerInterface $container
        ): EntityManagerInterface {
            $logger = Utils::safeContainerGet($container, LoggerInterface::class);

            // Doctrine entity paths.
            $paths = [
                "{$appConfig->projectRoot}/src/App/Model",
            ];

            // Create Doctrine directory if it doesn't exist.
            $doctrineDir = "{$appConfig->sharedDataDirectory}/doctrine/";
            if (!file_exists($doctrineDir)) {
                mkdir($doctrineDir, 0777, true);
            }

            $connection = DriverManager::getConnection([
                "driver" => "pdo_sqlite",
                "path" => "{$doctrineDir}/doctrineDb.sqlite",
            ]);
            $proxyDir = "_data-shared/doctrine/proxy";

            $doctrineConfig = ORMSetup::createAttributeMetadataConfiguration(
                $paths,
                $appConfig->isDev(),
                $proxyDir,
            );

            $em = new EntityManager($connection, $doctrineConfig);

            // Add SQLLogger in dev mode
            if ($appConfig->isDev() && $logger) {
                $em->getConnection()->getConfiguration()
                    ->setMiddlewares([new Middleware($logger)])
                ;
            }

            return $em;
        };

        // Alias EntityManager to ObjectManager
        $c[ObjectManager::class] = fn (EntityManagerInterface $x) => $x;

        return $c;
    }
}
