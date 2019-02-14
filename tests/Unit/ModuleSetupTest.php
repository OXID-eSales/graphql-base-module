<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\Framework\ModuleSetup;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ModuleSetupTest extends UnitTestCase
{

    public function testSignatureKeyGeneration()
    {
        $queryBuilderFactory = $this->getMockBuilder(QueryBuilderFactoryInterface::class)->getMock();
        /** @var MockObject|KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->expects($this->exactly(1))->method('createSignatureKey');

        $moduleSetup = new ModuleSetup($queryBuilderFactory, $keyRegistry);

        $moduleSetup->createSignatureKey();

    }
}
