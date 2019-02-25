<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Framework;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQl\Service\KeyRegistry;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleSetupTest extends UnitTestCase
{

    public function testSignatureKey()
    {
        $config = Registry::getConfig();
        $signatureKey = $config->getConfigParam(KeyRegistry::SIGNATUREKEY_KEY);
        $this->assertNotEmpty($signatureKey);
    }
}
