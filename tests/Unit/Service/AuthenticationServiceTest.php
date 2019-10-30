<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Service\AuthenticationService;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
# use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Service\LegacyServiceInterface;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AuthenticationServiceTest extends TestCase
{
    protected static $token = null;

    protected static $invalidToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';

    /** @var KeyRegistryInterface|MockObject */
    private $keyRegistry;

    /** @var LegacyServiceInterface|MockObject */
    private $legacyService;

    /** @var AuthenticationService */
    private $authenticationService;

    public function setUp()
    {
        $this->keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $this->keyRegistry->method('getSignatureKey')
            ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        $this->legacyService = $this->getMockBuilder(LegacyServiceInterface::class)->getMock();
        $this->authenticationService = new AuthenticationService($this->keyRegistry, $this->legacyService);
    }

    public function testCreateTokenFromRequest()
    {
        $requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $requestReader->method('getAuthToken')
                      ->will($this->onConsecutiveCalls(
                          null,
                          'invalid',
                          self::$invalidToken
                      ));
        $this->assertNull(AuthenticationService::createTokenFromRequest($requestReader));
        $e = null;
        try {
            AuthenticationService::createTokenFromRequest($requestReader);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        $this->assertInstanceOf(
            Token::class,
            AuthenticationService::createTokenFromRequest($requestReader)
        );
    }

    public function testCreateTokenWithInvalidCredentials()
    {
        $this->expectException(InvalidLoginException::class);
        $this->legacyService->method('checkCredentials')->willThrowException(new InvalidLoginException());
        $this->authenticationService->createAuthenticatedToken('foo', 'bar');
    }

    public function testIsLoggedWithoutToken()
    {
        $this->authenticationService->setToken(null);
        $this->assertFalse($this->authenticationService->isLogged());
    }

    public function testIsLoggedWithFormallyCorrectButInvalidToken()
    {
        $this->expectException(InvalidTokenException::class);
        $this->authenticationService->setToken(
            (new Parser())->parse(self::$invalidToken)
        );
        $this->authenticationService->isLogged();
    }

    public function testCreateTokenWithValidCredentials()
    {
        $this->legacyService->method('checkCredentials');
        $this->legacyService->method('getUserGroup')->willReturn(LegacyServiceInterface::GROUP_ADMIN);
        $this->legacyService->method('getShopUrl')->willReturn('https:/whatever.com');
        $this->legacyService->method('getShopId')->willReturn(1);

        self::$token = $this->authenticationService->createAuthenticatedToken('admin', 'admin');
        $this->assertInstanceOf(
            \Lcobucci\JWT\Token::class,
            self::$token
        );
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidToken()
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
    public function testIsLoggedWithValidForAnotherShopIdToken()
    {
        $this->expectException(InvalidTokenException::class);
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
    public function testIsLoggedWithValidForAnotherShopUrlToken()
    {
        $this->expectException(InvalidTokenException::class);

        $this->legacyService->method('getShopUrl')->willReturn('https:/other.com');
        $this->legacyService->method('getShopId')->willReturn(1);

        $this->authenticationService->setToken(
            self::$token
        );
        $this->authenticationService->isLogged();
    }

    public function testDeveloperToken()
    {
        $token = $this->authenticationService->createUnauthenticatedToken('developer', 'developer');
        $this->authenticationService->setToken($token);
        $this->authenticationService->isLogged();
    }
}
