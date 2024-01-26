<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @codeCoverageIgnore
 */
class ModuleSetup
{
    /** @var ModuleConfiguration */
    private $moduleConfiguration;

    /**
     * ModuleSetup constructor.
     */
    public function __construct(
        ModuleConfiguration $moduleConfiguration
    ) {
        $this->moduleConfiguration = $moduleConfiguration;
    }

    public function runSetup(): void
    {
        $this->moduleConfiguration->saveSignatureKey();
    }

    /**
     * Activation function for the module
     */
    public static function onActivate(): void
    {
        $container = (new ContainerBuilderFactory())->create()->getContainer();
        $container->compile();

        /** @var ModuleSetup $moduleSetup */
        $moduleSetup = $container->get(self::class);
        $moduleSetup->runSetup();

        // execute module migrations
        self::executeModuleMigrations();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }

    /**
     * Execute necessary module migrations on activate event
     */
    private static function executeModuleMigrations(): void
    {
        $migrations = (new MigrationsBuilder())->build();

        $output = new BufferedOutput();
        $migrations->setOutput($output);
        $neeedsUpdate = $migrations->execute('migrations:up-to-date', 'oe_graphql_base');

        if ($neeedsUpdate) {
            $migrations->execute('migrations:migrate', 'oe_graphql_base');
        }
    }
}
