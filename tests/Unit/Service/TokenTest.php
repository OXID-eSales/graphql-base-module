<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TokenTest extends BaseTestCase
{
    /** @var LegacyService|MockObject */
    private $legacy;

    /** @var TokenService */
    private $tokenService;

    public function setUp(): void
    {
        $this->legacy = $this->getMockBuilder(LegacyService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $this->legacy
        );

        $this->tokenService = new TokenService(
            null,
            $jwtConfigurationBuilder,
            $this->legacy,
            new EventDispatcher()
        );
    }

    public function testCreateTokenWithInvalidCredentials(): void
    {
        $this->expectException(InvalidLogin::class);
        $this->legacy
            ->method('login')
            ->willThrowException(new InvalidLogin('Username/password combination is invalid'));

        $this->tokenService->createToken('foo', 'bar');
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $userModel = $this->getUserModelStub('the_admin_oxid');

        $this->legacy
            ->method('login')
            ->willReturn(new User($userModel));
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);

        $token = $this->tokenService->createToken('admin', 'admin');

        $this->assertInstanceOf(
            Token::class,
            $token
        );
    }

    public function testCreateTokenWithValidCredentialsForBlockedUser(): void
    {
        $userModel = $this->getUserModelStub('the_admin_oxid');

        $this->legacy
            ->method('login')
            ->willReturn(new User($userModel));
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserGroupIds')
            ->willReturn(['foo', 'oxidblocked', 'bar']);

        $token = $this->tokenService->createToken('admin', 'admin');

        $this->assertInstanceOf(
            Token::class,
            $token
        );
    }

    public function testCreateAnonymousToken(): void
    {
        $this->legacy->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);

        $anonymousToken = $this->tokenService->createToken();

        $this->assertInstanceOf(
            Token::class,
            $anonymousToken
        );

        $this->assertEmpty(
            $anonymousToken->claims()->get(TokenService::CLAIM_USERNAME)
        );
    }
}
