<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration;

use OxidEsales\GraphQl\Service\KeyRegistry;
use PHPUnit\Framework\TestCase;

class TestKeyRegistry extends TestCase
{
    public function testKeyGeneration()
    {
        $keyRegistry = new KeyRegistry();
        $this->assertNull($keyRegistry->getSignatureKey());
        $keyRegistry->createSignatureKey();
        $key = $keyRegistry->getSignatureKey();
        $this->assertNotNull($key);
        $keyRegistry->createSignatureKey();
        $newKey = $keyRegistry->getSignatureKey();
        // Once generated, the key should never change
        $this->assertEquals($key, $newKey);
    }

}
