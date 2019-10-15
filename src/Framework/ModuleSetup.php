<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;

class ModuleSetup
{
    /** @var KeyRegistryInterface */
    private $keyRegistry = null;

    /** @var ModuleSettingBridgeInterface */
    private $moduleSettings = null;

    /**
     * ModuleSetup constructor.
     *
     * @param KeyRegistryInterface $keyRegistry
     */
    public function __construct(KeyRegistryInterface $keyRegistry, ModuleSettingBridgeInterface $moduleSettings)
    {
        $this->keyRegistry = $keyRegistry;
        $this->moduleSettings = $moduleSettings;
    }

    public function runSetup(): void
    {
        $this->moduleSettings->save(
            $this->keyRegistry::signatureKeyName,
            $this->keyRegistry->generateSignatureKey(),
            'oe/graphql-base'
        );
    }

    /**
     * Activation function for the module
     */
    public static function onActivate(): void
    {
        /** @var ModuleSetup $moduleSetup */
        $moduleSetup = ContainerFactory::getInstance()->getContainer()->get(ModuleSetup::class);
        $moduleSetup->runSetup();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }
}
