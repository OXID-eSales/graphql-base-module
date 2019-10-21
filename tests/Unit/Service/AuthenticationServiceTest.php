<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Framework\RequestReader;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Service\AuthenticationService;
use OxidEsales\GraphQL\Service\KeyRegistry;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Parser;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected static $authentication = null;

    protected static $token = null;

    protected static $invalidToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';

    protected static $signature = null;

    protected static $container = null;

    public static function getToken(): ?string
    {
        if (self::$token === null) {
            return null;
        }
        return (string) self::$token;
    }

    /**
     * this empty methods prevents phpunit from resetting
     * invocation mocker and therefore we can use the same
     * mocks for all tests and do not need to reinitialize
     * the container for every test in this file which
     * makes the whole thing pretty fast :-)
     */
    protected function verifyMockObjects()
    {
    }

    public function setUp(): void
    {
        if (self::$container !== null) {
            return;
        }

        $containerFactory = new TestContainerFactory();
        self::$container = $containerFactory->create();

        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')
                    ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        self::$container->set(
            KeyRegistryInterface::class,
            $keyRegistry
        );
        self::$container->autowire(
            KeyRegistryInterface::class,
            KeyRegistry::class
        );

        self::$container->compile();

        self::$authentication = self::$container->get(AuthenticationServiceInterface::class);
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
        self::$authentication->createToken('foo', 'bar', 999);
    }

    public function testIsLoggedWithoutToken()
    {
        self::$authentication->setToken(null);
        $this->assertFalse(self::$authentication->isLogged());
    }

    public function testIsLoggedWithFormallyCorrectButInvalidToken()
    {
        $this->expectException(InvalidTokenException::class);
        self::$authentication->setToken(
           (new Parser())->parse(self::$invalidToken) 
        );
        self::$authentication->isLogged();
    }

    public function testCreateTokenWithValidCredentials()
    {
        self::$token = self::$authentication->createToken('admin', 'admin', 1);
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
        self::$authentication->setToken(
            self::$token
        );
        $this->assertTrue(self::$authentication->isLogged());
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testIsLoggedWithValidForAnotherShopIdToken()
    {
        $oldConfig = Registry::getConfig();
        $config = $this->getMockBuilder(Config::class)->getMock();
        $config->method('getShopId')
               ->willReturn(-1);
        Registry::set(Config::class, $config);
        try {
            self::$authentication->isLogged();
        } catch (InvalidTokenException $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        Registry::set(Config::class, $oldConfig);
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testIsLoggedWithValidForAnotherShopUrlToken()
    {
        $oldConfig = Registry::getConfig();
        $config = $this->getMockBuilder(Config::class)->getMock();
        $config->method('getShopUrl')
               ->willReturn('invalid');
        Registry::set(Config::class, $config);
        try {
            self::$authentication->isLogged();
        } catch (InvalidTokenException $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        Registry::set(Config::class, $oldConfig);
    }
}
