<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Service\TokenValidator;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TokenValidatorTest extends BaseTestCase
{
    public function testTokenShopIdValidation()
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId']);
        $legacy->method('login')->willReturn($this->getUserStub());
        $legacy->method('getShopId')->willReturn(1);

        $token = $this->getTokenService($legacy)->createToken("admin", "admin");

        // token is valid
        $validator = $this->getTokenValidator($legacy);
        $validator->validateToken($token);

        $legacy = $this->createPartialMock(LegacyService::class, ['getShopId']);
        $legacy->method('getShopId')->willReturn(-1);

        // token is invalid
        $validator = $this->getTokenValidator($legacy);
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }

    public function testTokenShopUrlValidation()
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopUrl', 'getShopId']);
        $legacy->method('login')->willReturn($this->getUserStub());
        $legacy->method('getShopUrl')->willReturn('http://original.url');

        $token = $this->getTokenService($legacy)->createToken("admin", "admin");

        // token is valid
        $validator = $this->getTokenValidator($legacy);
        $validator->validateToken($token);

        $legacy = $this->createPartialMock(LegacyService::class, ['getShopUrl', 'getShopId']);
        $legacy->method('getShopUrl')->willReturn('http://other.url');

        // token is invalid
        $validator = $this->getTokenValidator($legacy);
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }

    public function testTokenUserInBlockedGroup()
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getUserGroupIds']);
        $legacy->method('login')->willReturn($this->getUserStub());
        $legacy->method('getUserGroupIds')->willReturn(['foo', 'oxidblocked', 'bar']);

        $token = $this->getTokenService($legacy)->createToken("admin", "admin");

        $validator = $this->getTokenValidator($legacy);
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }

    protected function getTokenValidator($legacy)
    {
        return new TokenValidator(
            $this->getJwtConfigurationBuilder($legacy),
            $legacy
        );
    }

    protected function getTokenService($legacy, $token = null)
    {
        return new TokenService(
            $token,
            $this->getJwtConfigurationBuilder($legacy),
            $legacy,
            $this->createPartialMock(EventDispatcherInterface::class, [])
        );
    }

    protected function getJwtConfigurationBuilder($legacy)
    {
        return new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $legacy
        );
    }

    protected function getUserStub()
    {
        return new UserDataType(oxNew(UserModel::class));
    }
}
