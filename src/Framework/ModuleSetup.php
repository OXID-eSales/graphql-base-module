<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;

class ModuleSetup
{
    /** @var  KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    /**
     * ModuleSetup constructor.
     *
     * @param KeyRegistryInterface $keyRegistry
     */
    public function __construct(KeyRegistryInterface $keyRegistry)
    {
        $this->keyRegistry = $keyRegistry;
    }

    private function createSignatureKey()
    {
        $this->keyRegistry->createSignatureKey();
    }

    public function runSetup()
    {
        $this->createSignatureKey();
    }

    /**
     * Activation function for the module
     */
    public static function onActivate()
    {
        /** @var ModuleSetup $moduleSetup */
        $moduleSetup = ContainerFactory::getInstance()->getContainer()->get(ModuleSetup::class);
        $moduleSetup->runSetup();
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactive()
    {
    }
}
