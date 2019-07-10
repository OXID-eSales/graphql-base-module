<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Framework;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleSetupTest extends UnitTestCase
{
    public function testSignatureKey()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $container->get(KeyRegistryInterface::class);
        $signatureKey = $keyRegistry->getSignatureKey();
        $this->assertNotEmpty($signatureKey);
    }

}
