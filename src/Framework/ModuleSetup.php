<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;

/**
 * @codeCoverageIgnore
 */
class ModuleSetup
{
    /** @var KeyRegistry */
    private $keyRegistry;

    /** @var ModuleSettingBridgeInterface */
    private $moduleSettings;

    /**
     * ModuleSetup constructor.
     */
    public function __construct(
        KeyRegistry $keyRegistry,
        ModuleSettingBridgeInterface $moduleSettings
    ) {
        $this->keyRegistry    = $keyRegistry;
        $this->moduleSettings = $moduleSettings;
    }

    public function runSetup(): void
    {
        $this->moduleSettings->save(
            KeyRegistry::SIGNATUREKEYNAME,
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
        $moduleSetup = ContainerFactory::getInstance()->getContainer()->get(self::class);
        $moduleSetup->runSetup();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }
}
