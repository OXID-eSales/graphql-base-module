<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Parser;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Service\AuthenticationService;
use OxidEsales\GraphQL\Base\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Base\Service\Legacy as LegacyService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected static $token = null;

    // phpcs:disable
    protected static $invalidToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';

    // phpcs:enable

    /** @var KeyRegistryInterface|MockObject */
    private $keyRegistry;

    /** @var LegacyService|MockObject */
    private $legacyService;

    /** @var AuthenticationService */
    private $authenticationService;

    public function setUp(): void
    {
        $this->keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $this->keyRegistry->method('getSignatureKey')
             ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        $this->legacyService         = $this->getMockBuilder(LegacyService::class)
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $this->authenticationService = new AuthenticationService($this->keyRegistry, $this->legacyService);
    }

    public function testCreateTokenWithInvalidCredentials(): void
    {
        $this->expectException(InvalidLogin::class);
        $this->legacyService->method('checkCredentials')->willThrowException(new InvalidLogin());
        $this->authenticationService->createToken('foo', 'bar');
    }

    public function testIsLoggedWithoutToken(): void
    {
        $this->authenticationService->setToken(null);
        $this->assertFalse($this->authenticationService->isLogged());
    }

    public function testIsLoggedWithFormallyCorrectButInvalidToken(): void
    {
        $this->expectException(InvalidToken::class);
        $this->authenticationService->setToken(
            (new Parser())->parse(self::$invalidToken)
        );
        $this->authenticationService->isLogged();
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $this->legacyService->method('checkCredentials');
        $this->legacyService->method('getUserGroup')->willReturn(LegacyService::GROUP_ADMIN);
        $this->legacyService->method('getShopUrl')->willReturn('https:/whatever.com');
        $this->legacyService->method('getShopId')->willReturn(1);

        self::$token = $this->authenticationService->createToken('admin', 'admin');
        $this->assertInstanceOf(
            \Lcobucci\JWT\Token::class,
            self::$token
        );
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidToken(): void
    {
        $this->legacyService->method('getShopUrl')->willReturn('https:/whatever.com');
        $this->legacyService->method('getShopId')->willReturn(1);
        $this->authenticationService->setToken(
            self::$token
        );
        $this->assertTrue($this->authenticationService->isLogged());
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidForAnotherShopIdToken(): void
    {
        $this->expectException(InvalidToken::class);
        $this->legacyService->method('getShopUrl')->willReturn('https:/whatever.com');
        $this->legacyService->method('getShopId')->willReturn(-1);
        $this->authenticationService->setToken(
            self::$token
        );
        $this->authenticationService->isLogged();
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testIsLoggedWithValidForAnotherShopUrlToken(): void
    {
        $this->expectException(InvalidToken::class);

        $this->legacyService->method('getShopUrl')->willReturn('https:/other.com');
        $this->legacyService->method('getShopId')->willReturn(1);

        $this->authenticationService->setToken(
            self::$token
        );
        $this->authenticationService->isLogged();
    }
}
