<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;

/*
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
*/

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
            'sJsonWebTokenSignature',
            $this->keyRegistry->generateSignatureKey(),
            'oe/graphql-base'
        );
    }

    /**
     * Activation function for the module
     */
    public static function onActivate(): void
    {
        /*
        $moduleConfigurationDaoBridge = $this->getContainer()->get(ModuleConfigurationDaoBridgeInterface::class);
        $moduleConfiguration = $moduleConfigurationDaoBridge->get('oe/graphql-base');

        if (!empty($moduleConfiguration->getModuleSettings())) {
            foreach ($variables as $name => $value) {
                foreach ($moduleConfiguration->getModuleSettings() as $moduleSetting) {
                    if ($moduleSetting->getName() === $name) {
                        if ($moduleSetting->getType() === 'aarr') {
                            $value = $this->_multilineToAarray($value);
                        }
                        if ($moduleSetting->getType() === 'arr') {
                            $value = $this->_multilineToArray($value);
                        }
                        if ($moduleSetting->getType() === 'bool') {
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        }
                        $moduleSetting->setValue($value);
                    }
                }
            }

            $moduleConfigurationDaoBridge->save($moduleConfiguration);
        }
         */

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
