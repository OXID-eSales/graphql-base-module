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
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BaseTestCase extends TestCase
{
    protected function getKeyRegistryMock(): KeyRegistry
    {
        $keyRegistry = $this->getMockBuilder(KeyRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $keyRegistry->method('getSignatureKey')
            ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');

        return $keyRegistry;
    }

    protected function getUserModelStub(?string $id = null): UserModel
    {
        $userModelStub = $this->createPartialMock(UserModel::class, ['getFieldData']);

        if ($id) {
            $userModelStub->setId($id);
        }

        return $userModelStub;
    }

    protected function getTokenValidator($legacy): TokenValidator
    {
        return new TokenValidator(
            $this->getJwtConfigurationBuilder($legacy),
            $legacy
        );
    }

    protected function getTokenService($legacy, $token = null): TokenService
    {
        return new TokenService(
            $token,
            $this->getJwtConfigurationBuilder($legacy),
            $legacy,
            $this->createPartialMock(EventDispatcherInterface::class, [])
        );
    }

    protected function getJwtConfigurationBuilder($legacy = null): JwtConfigurationBuilder
    {
        return new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $legacy
        );
    }

    protected function getUserDataStub(UserModel $model = null): UserDataType
    {
        return new UserDataType(
            $model ?: $this->createPartialMock(UserModel::class, ['getFieldData'])
        );
    }
}
