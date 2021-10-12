<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;

/**
 * @codeCoverageIgnore
 */
class ModuleSetup
{
    /** @var ModuleConfiguration */
    private $moduleConfiguration;

    /** @var ModuleSettingBridgeInterface */
    private $moduleSettings;

    /**
     * ModuleSetup constructor.
     */
    public function __construct(
        ModuleConfiguration          $moduleConfiguration,
        ModuleSettingBridgeInterface $moduleSettings
    ) {
        $this->moduleConfiguration    = $moduleConfiguration;
        $this->moduleSettings         = $moduleSettings;
    }

    public function runSetup(): void
    {
        $this->moduleSettings->save(
            ModuleConfiguration::SIGNATUREKEYNAME,
            $this->moduleConfiguration->generateSignatureKey(),
            'oe_graphql_base'
        );
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
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }
}
