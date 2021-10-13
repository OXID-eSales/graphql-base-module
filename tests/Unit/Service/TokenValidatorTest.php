<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class TokenValidatorTest extends BaseTestCase
{
    public function testTokenShopIdValidation(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getShopUrl']);
        $legacy->method('login')->willReturn($this->getUserDataStub());
        $legacy->method('getShopId')->willReturn(1);

        $token = $this->getTokenService($legacy)->createToken('admin', 'admin');

        // token is valid
        $validator = $this->getTokenValidator($legacy);
        $validator->validateToken($token);

        $legacy = $this->createPartialMock(LegacyService::class, ['getShopId', 'getShopUrl']);
        $legacy->method('getShopId')->willReturn(-1);

        // token is invalid
        $validator = $this->getTokenValidator($legacy);
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }

    public function testTokenShopUrlValidation(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopUrl', 'getShopId']);
        $legacy->method('login')->willReturn($this->getUserDataStub());
        $legacy->method('getShopUrl')->willReturn('http://original.url');

        $token = $this->getTokenService($legacy)->createToken('admin', 'admin');

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

    public function testTokenUserInBlockedGroup(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getShopUrl', 'getUserGroupIds']);
        $legacy->method('login')->willReturn($this->getUserDataStub());
        $legacy->method('getUserGroupIds')->willReturn(['foo', 'oxidblocked', 'bar']);

        $token = $this->getTokenService($legacy)->createToken('admin', 'admin');

        $validator = $this->getTokenValidator($legacy);
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }

    public function testExpiredToken(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getShopUrl', 'getUserGroupIds']);
        $legacy->method('login')->willReturn($this->getUserDataStub());
        $legacy->method('getUserGroupIds')->willReturn(['foo', 'bar']);
        $validator = $this->getTokenValidator($legacy);

        $token = $this->getTokenService($legacy, null, '+1 hours')->createToken('admin', 'admin');
        $validator->validateToken($token);

        $token = $this->getTokenService($legacy, null, '-1 hours')->createToken('admin', 'admin');
        $this->expectException(InvalidToken::class);
        $validator->validateToken($token);
    }
}
