<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Service\AuthenticationService;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Framework\RequestReader;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Service\KeyRegistry;

class AuthenticationServiceTest extends TestCase
{

    protected static $authentication = null;

    protected static $token = null;
    
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

        $requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $requestReader->method('getAuthToken')
                      ->will($this->returnCallback(function() {
                          return AuthenticationServiceTest::getToken();
                      }));
        self::$container->set(
            RequestReaderInterface::class,
            $requestReader
        );
        self::$container->autowire(
            RequestReaderInterface::class,
            RequestReader::class
        );

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

    public function testCreateTokenWithInvalidCredentials()
    {
        $this->expectException(InvalidLoginException::class);
        self::$authentication->createToken('foo', 'bar', 999);
    }

    public function testIsLoggedWithoutToken()
    {
        $this->assertFalse(self::$authentication->isLogged());
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
        $this->assertTrue(self::$authentication->isLogged());
    }

}
