<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit;

use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    // phpcs:enable

    protected function getKeyRegistryMock(): KeyRegistry
    {
        $keyRegistry = $this->getMockBuilder(KeyRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $keyRegistry->method('getSignatureKey')
            ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');

        return $keyRegistry;
    }

    protected function getUserModelStub(?string $id = null)
    {
        $userModelStub = $this->createPartialMock(UserModel::class, []);

        if ($id) {
            $userModelStub->setId($id);
        }

        return $userModelStub;
    }
}
