<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class TokenTest extends BaseTestCase
{
    public function testCreateTokenWithInvalidCredentials(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login']);
        $legacy->method('login')->willThrowException(new InvalidLogin('Username/password combination is invalid'));

        $this->expectException(InvalidLogin::class);
        $this->getTokenService($legacy)->createToken('foo', 'bar');
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId']);
        $legacy->method('login')->willReturn($this->getUserDataStub($this->getUserModelStub('the_admin_oxid')));

        $token = $this->getTokenService($legacy)->createToken('admin', 'admin');

        $this->assertInstanceOf(Token::class, $token);
    }

    public function testCreateTokenWithValidCredentialsForBlockedUser(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getUserGroupIds']);
        $legacy->method('login')->willReturn($this->getUserDataStub($this->getUserModelStub('the_admin_oxid')));
        $legacy->method('getUserGroupIds')->willReturn(['foo', 'oxidblocked', 'bar']);

        $token = $this->getTokenService($legacy)->createToken('admin', 'admin');

        $this->assertInstanceOf(Token::class, $token);
    }

    public function testCreateAnonymousToken(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId']);
        $legacy->method('login')->willReturn($this->getUserDataStub($this->getUserModelStub()));

        $anonymousToken = $this->getTokenService($legacy)->createToken();

        $this->assertInstanceOf(Token::class, $anonymousToken);
        $this->assertEmpty($anonymousToken->claims()->get(TokenService::CLAIM_USERNAME));
    }
}
