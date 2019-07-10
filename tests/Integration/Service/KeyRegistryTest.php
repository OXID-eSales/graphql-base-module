<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Service;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class KeyRegistryTest extends UnitTestCase
{

    public function testKeyGeneration()
    {
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $keyRegistry = $container->get(KeyRegistryInterface::class);
        $keyRegistry->createSignatureKey();
        $key = $keyRegistry->getSignatureKey();
        $this->assertNotNull($key);
        $keyRegistry->createSignatureKey();
        $newKey = $keyRegistry->getSignatureKey();
        // Once generated, the key should never change
        $this->assertEquals($key, $newKey);
    }

}
