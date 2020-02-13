<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\TestingLibrary\Services\ModuleInstaller\ModuleInstaller;

abstract class EnterpriseTestCase extends TestCase
{
    protected function setUp(): void
    {
        if ($this->getConfig()->getEdition() !== 'EE') {
            $this->markTestSkipped("Skip EE related tests for CE/PE edition");
            return;
        }

        parent::setUp();
    }
}
