<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;

/**
 * @codeCoverageIgnore
 */
class ModuleSetup
{
    public function __construct(
        private readonly ModuleConfiguration $moduleConfiguration
    ) {
    }

    public function runSetup(): void
    {
        $this->moduleConfiguration->generateAndSaveSignatureKey();
    }

    /**
     * Activation function for the module
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function onActivate(): void
    {
        ContainerFactory::resetContainer();

        /** @var ModuleSetup $moduleSetup */
        $moduleSetup = ContainerFacade::get(self::class);
        $moduleSetup->runSetup();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }
}
