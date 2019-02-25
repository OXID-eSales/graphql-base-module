<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQl\Service\KeyRegistry;
use OxidEsales\TestingLibrary\UnitTestCase;

class KeyRegistryTest extends UnitTestCase
{
    public function setUp()
    {
        Registry::getConfig()->setConfigParam(KeyRegistry::SIGNATUREKEY_KEY, null);
    }

    public function testKeyGeneration()
    {
        $keyRegistry = new KeyRegistry();
        $keyRegistry->createSignatureKey();
        $key = $keyRegistry->getSignatureKey();
        $this->assertNotNull($key);
        $keyRegistry->createSignatureKey();
        $newKey = $keyRegistry->getSignatureKey();
        // Once generated, the key should never change
        $this->assertEquals($key, $newKey);
    }

}
